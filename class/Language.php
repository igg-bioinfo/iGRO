<?php

class Language {

    public static $iso = "";
    public static $country_code = "";
    public static $name = "";
    public static $name_translated = "";
    public static $is_right_dir = false;
    public static $area_translations = [];

    public static function set($iso = 'en') {
        self::$iso = $iso;

        $sql = 'SELECT L.* FROM language L  WHERE  L.languageiso = ? ORDER BY translated';
        $languages = Database::read($sql, [self::$iso]);
        if (count($languages) == 1) {
            $language = $languages[0];
            self::$name_translated = $language["english"];
            self::$country_code = $language["country_code"];
            self::$name = $language["translated"];
            self::$is_right_dir = $language["is_right"] . '' == '1';
            self::add_area('general');
        }
    }

    public static function get_all($orderby = 'translated') {
        $sql = 'SELECT L.* FROM language L ORDER BY ' . $orderby;
        return Database::read($sql, []);
    }

    public static function add_area($area_text) {
        if (self::$iso.'' == '') { self::$iso = Config::LANGUAGEISO; }
        $area_text = strtolower($area_text);
        $sql = "SELECT DISTINCT TE.label_text,
                IFNULL(T.languageiso, TE.languageiso) AS languageiso, IFNULL(T.translation, TE.translation) AS translation
            FROM language_translation TE
            LEFT OUTER JOIN language_translation T ON T.label_text = TE.label_text AND T.languageiso = ? AND T.area_text = TE.area_text
            WHERE TE.languageiso = 'en' and TE.area_text = ? ";
        $translations = Database::read($sql, array(self::$iso, $area_text), 'label_text');
        self::$area_translations[$area_text] = $translations;
    }

    public static function get_translation($label_text, $area_text = 'general') {
        $area_text = strtolower($area_text);
        $text = '';
        if (isset(self::$area_translations[$area_text])) {
            $translations = self::$area_translations[$area_text];
            foreach ($translations as $translation) {
                if (strtoupper($translation["label_text"]) == strtoupper($label_text)) {
                    $text = self::get_sanitized_value($translation);
                    break;
                }
            }
        }
        if ($text == '') {
            $text = $label_text;
        }
        return $text;
    }

    public static function get_translation_associative($label_text, $area_text = 'general') {
        $area_text = strtolower($area_text);

        if (isset(self::$area_translations[$area_text][$label_text])) {
            return self::get_sanitized_value(self::$area_translations[$area_text][$label_text]);
        }

        return $label_text;
    }

    public static function find($label_text, $areas = []) {
        if (!is_array($areas)) {
            $areas = strtolower($areas);
            $areas = [$areas];
        }

        foreach ($areas as $area) {
            $area = strtolower($area);
            if (!isset(self::$area_translations[$area])) {
                self::add_area($area);
            }
        }

        foreach (array_keys(self::$area_translations) as $area) {
            if (isset(self::$area_translations[strtolower($area)][$label_text])) {
                return self::get_translation_associative($label_text, $area);
            }
        }

        return (Config::SITEVERSION == 'TEST' && $label_text.'' != '' ? 'TBT_' : ''). $label_text;
    }

    public static function find_or_default($label_text, $default = null, $areas = []) {
        $res = self::find($label_text, $areas);
        if ($res && !Strings::startsWith($res, 'TBT_')) {
            return $res;
        }
        if (!is_null($default)) {
            return $default;
        }
        return (Config::SITEVERSION == 'TEST' && $label_text.'' != '' ? 'TBT_' : ''). $label_text;
    }

    public static function get_sanitized_value($translation) {
        $text = '';
        $is_geo = strtolower($translation["languageiso"]) . '' == 'geo';
        $text = ($is_geo ? '<font face="AcadNusx">' : '') . $translation["translation"] . ($is_geo ? '</font>' : '');
        return $text;
    }

    //--------------CHECK LANGUAGES
    public static function check_tag_translations() {
        $sql = "SELECT translation, languageiso, id_translation
            FROM label_translation L WHERE translation LIKE '%<%' OR translation LIKE '%>%' ";
        $translations = Database::read($sql, array());
        $all = 0;
        $errors = [];
        for ($l = 0; $l < count($translations); $l++) {
            $text = $translations[$l][0];
            $text = str_replace('<br>', '', $text);
            $text = str_replace('<br />', '', $text);
            $text = str_replace('</br>', '', $text);
            if (!Strings::contains($text, '<') && !Strings::contains($text, '>')) {
                continue;
            }
            $text = self::check_tags($text);
            if ($text && !Strings::contains($text, '38')) {
                $errors[] = [$translations[$l][2], $translations[$l][1], htmlspecialchars($text)];
                $all++;
            }
        }
        return $errors;
    }

    private static function check_tags($html) {
        $result = [];
        preg_match_all('/<([a-zA-Z0-9]+)>/', $html, $result);
        $goodopentags = $result[1];
        if (count($goodopentags) == 0) {
            return $html;
        }

        preg_match_all('/<([a-zA-Z0-9]+)(?: .*)?(?<![\/|\/ ])>/', $html, $result);
        $openedtags = $result[1];

        preg_match_all('#<\/([a-zA-Z0-9]+)>#iU', $html, $result);
        $closedtags = $result[1];

        $openedtags = array_reverse($openedtags);

        for ($i = 0; $i < count($openedtags); $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                return $html;
            }
        }

        $closedtags = array_reverse($closedtags);

        for ($i = 0; $i < count($closedtags); $i++) {
            if (!in_array($closedtags[$i], $openedtags)) {
                return $html;
            }
        }
        return false;
    }

    public static function save($field, $iso, $group, $translation){
        $found = Database::read("SELECT * FROM language_translation WHERE label_text = ? AND languageiso = ?", [$field, $iso]);
        if (count($found) == 0) {
            $sql = "INSERT INTO language_translation (area_text, translation, label_text, languageiso) SELECT ?, ?, ?, ?;";
        } else {
            $sql = "UPDATE language_translation SET area_text = ?, translation = ? WHERE label_text = ? AND languageiso = ?;";
        }
        Database::edit($sql, [$group, $translation, $field, $iso]);
    }
    public static function get($field, $iso){
        $found = Database::read("SELECT translation FROM language_translation WHERE label_text = ? AND languageiso = ?", [$field, $iso]);
        return count($found) == 0 ? '' : $found[0]['translation'];
    }
}
