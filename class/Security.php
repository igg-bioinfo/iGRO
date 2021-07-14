<?php

class Security {

    private static $token_text = '';
    private static $tot_rand = 0;
    private static $token_expiration = 3600;
    //--------------- TOKEN ID = %
    //--------------- TOKEN VALUE = £
    private static $hidden_text = '<input type="hidden" id="%" name="%" value="£" />';
    private static $post_string_text = '&%=£';
    private static $post_json_text = ', "%" : "£"';
    private static $post_data_text = '% : £';

    private static $allowed_tags = ['em', 'span', 'strong', 'a', 'ul', 'li', 'i', 'b', 'u', 'br'];

    const TYPE_HIDDEN = 0;
    const TYPE_POST_STRING = 1;
    const TYPE_POST_JSON = 2;
    const TYPE_POST_DATA = 3;

    private static function set_token_text() {
        self::$token_text = URL::$prefix . 'token_';
    }

    public static function set_token($type = 0) {
        self::set_token_text();
        $index = 0;
        foreach ($_SESSION as $key => $value) {
            if (strpos($key, self::$token_text . "id") === 0) {
                $index++;
            }
        }
        $html_hidden = '';
        $index_rnd = rand(0, self::$tot_rand);
        for ($r = 0; $r <= self::$tot_rand; $r++) {
            $temp_html = '';
            switch ($type) {
                default:
                case self::TYPE_HIDDEN:
                    $temp_html = self::$hidden_text;
                    break;

                case self::TYPE_POST_STRING:
                    $temp_html = self::$post_string_text;
                    break;

                case self::TYPE_POST_JSON:
                    $temp_html = self::$post_json_text;
                    break;

                case self::TYPE_POST_DATA:
                    $temp_html = self::$post_data_text;
                    break;
            }
            $temp_id = self::random(10);
            $temp_value = hash('sha256', self::random(500));
            if ($r == $index_rnd) {
                $_SESSION[self::$token_text . 'value_' . $temp_id . '_' . ($index + 1)] = $temp_value;
                if ($type != self::TYPE_HIDDEN) {
                    $_SESSION[self::$token_text . 'ajax_' . $temp_id . '_' . ($index + 1)] = time() + self::$token_expiration;
                }
            }
            $temp_html = str_replace('%', self::$token_text . $temp_id . '_' . ($index + 1), $temp_html);
            $temp_html = str_replace('£', $temp_value, $temp_html);
            $html_hidden .= $temp_html;
        }
        return $html_hidden;
    }

    public static function check_post_token() {
        self::set_token_text();
        $found = true;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $found = false;
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, self::$token_text . 'value') === 0) {
                    $key_id = str_replace(self::$token_text . 'value_', '', $key);
                    if (Security::sanitize(INPUT_POST, self::$token_text . $key_id) == $_SESSION[self::$token_text . 'value_' . $key_id]) {
                        $found = true;
                        if (isset($_SESSION[self::$token_text . 'ajax_' . $key_id])) {
                            // if time is greater than token validity time unset token else do not unset anything
                            if(time() > $_SESSION[self::$token_text . 'ajax_' . $key_id]) { 
                                unset($_SESSION[self::$token_text . 'ajax_' . $key_id]);
                            } else {
                                break;
                            }
                        }
                        unset($_SESSION[self::$token_text . 'value_' . $key_id]);
                        break;
                    }
                }
            }
        }
        return $found;
    }

    public static function random($len) {
        self::set_token_text();
        if (function_exists('openssl_random_pseudo_bytes')) {
            $byteLen = intval(($len / 2) + 1);
            $return = substr(bin2hex(openssl_random_pseudo_bytes($byteLen)), 0, $len);
        }
        if (empty($return)) {
            for ($i = 0; $i < $len; ++$i) {
                if ($i % 2 == 0) {
                    mt_srand(time() % 2147 * 1000000 + (double) microtime() * 1000000);
                }
                $rand = 48 + mt_rand() % 64;

                if ($rand > 57)
                    $rand += 7;
                if ($rand > 90)
                    $rand += 6;
                if ($rand == 123)
                    $rand = 52;
                if ($rand == 124)
                    $rand = 53;
                $return .= chr($rand);
            }
        }
        return $return;
    }

    public static function sanitize($type, $var_name, $filter = FILTER_DEFAULT, $options = null, $no_trim = false, $removeHtmlTags = true) {
        $filtered = filter_input($type, $var_name, $filter, $options);
        if (!is_array($filtered) && !is_array(json_decode($filtered))) {
            $filtered = filter_var($filtered, FILTER_SANITIZE_FULL_SPECIAL_CHARS, $options);
            //$filtered = htmlspecialchars($filtered, ENT_HTML5);
        }
        return $filtered;

        echo $var_name.' = '.filter_input($type, $var_name, FILTER_DEFAULT, $options).'<br>';
        
        /**
         * Esempi di risultati ([no_trim = TRUE]: contenuto ipotizzato dalla prima parola):
         * <script>123</script> --> INT (script viene rimosso)
         * <b>123</b> --> FULL SPECIAL CHARS (i tag vengono sanificati)
         * 123 abc --> INT (abc viene rimosso)
         * 123abc --> FULL SPECIAL CHARS
         * 123abc <b>test</b> <script>test</script> --> FULL SPECIAL CHARS (script viene rimosso, b viene sanificato)
         * 12.345 abc! --> FLOAT (abc! viene rimosso)
         * abc! 12.345 --> FULL SPECIAL CHARS
         * test@gaslini.org --> EMAIL
         * test@gaslini.org testo extra --> EMAIL (gli spazi vengono rimossi)
         * testo extra test@gaslini.org --> FULL SPECIAL CHARS
         *
         * [no_trim = FALSE] consente di mantenere il testo in eccesso.
         * 123 abc --> FULL SPECIAL CHARS
         * test@gaslini.org testo extra --> FULL SPECIAL CHARS
         * 123<script>456</script> --> INT (script viene rimosso)
         *
         * [removeHtmlTags = FALSE] consente di mantenere tutti i tag HTML.
         */
        $filter_consts = get_defined_constants(true)["filter"];
        $type_str = array_search($type, $filter_consts);
        $log_trimmed = ($no_trim) ? "NO_TRIM" : "TRIM";
        $html_tags_allowed = "<b><i><u><strong><p><ul><ol><li><table><tr><th><br>";

        $input = filter_input($type, $var_name, FILTER_DEFAULT, $options);
        if (!is_array($input)) {
            $input_filtered = strip_tags($input, $html_tags_allowed);
        }

        if ($filter) {
            // LOG // $log_content = date("[d/m/Y H:i:s]") . " " . $type_str . ":" . $var_name . " --> SKIPPED:" . array_search($filter, $filter_consts) . PHP_EOL;
            // LOG // file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/sanification.log', $log_content, FILE_APPEND | LOCK_EX);
            // Filtro già impostato: skip dell'ipotesi, ma rimozione dei tag pericolosi se necessario.
            if ($filter === FILTER_SANITIZE_FULL_SPECIAL_CHARS && $removeHtmlTags)
                return filter_var($input_filtered, FILTER_SANITIZE_FULL_SPECIAL_CHARS, $options);
            else
                return filter_input($type, $var_name, $filter, $options);
        }

        // TRIM: ipotesi del contenuto basato sulla prima parola,
        // escludendo tag.
        $input_exploded_tmp = explode(' ', trim($input_filtered));
        $input_exploded = (strpos($input_exploded_tmp[0], '<') !== false) ?
                strstr($input_exploded_tmp[0], '<', true) : $input_exploded_tmp[0];

        $detected_type = FILTER_SANITIZE_FULL_SPECIAL_CHARS; // DEFAULT: htmlspecialchars
        $final_options = null;

        // LISTA DEI CASI SPECIALI
        $filter_list = [
            [FILTER_VALIDATE_FLOAT, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND],
            [FILTER_VALIDATE_INT, FILTER_SANITIZE_NUMBER_INT, $options],
            [FILTER_VALIDATE_BOOLEAN, FILTER_DEFAULT, $options],
            [FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL, $options],
            [FILTER_VALIDATE_URL, FILTER_SANITIZE_URL, $options],
        ];

        foreach ($filter_list as $fl) {
            // Ipotesi del contenuto basato sulla prima parola.
            // Se nessuna ipotesi riesce, applica il filtro più permissivo (FULL_SPECIAL_CHARS).
            if (filter_var((($no_trim) ? $input : $input_exploded), $fl[0], $fl[2])) {
                $detected_type = $fl[1];
                $final_options = $fl[2];
            }
        }

        /* LOG
          if (CONFIG::SITEVERSION == 'TEST')
          {
          $log_content = date("[d/m/Y H:i:s]") . " " . $type_str . ":" . $var_name . ":" . $log_trimmed . " = 【" . (($no_trim) ? $input : $input_exploded) . "】 --> " . array_search($detected_type, $filter_consts) . PHP_EOL;
          file_put_contents($_SERVER["DOCUMENT_ROOT"] . '/sanification.log', $log_content, FILE_APPEND | LOCK_EX);
          } */

        if ($detected_type === FILTER_SANITIZE_FULL_SPECIAL_CHARS && $removeHtmlTags) {
            // Se il tipo è stato determinato essere un FULL_SPECIAL_CHARS,
            // vengono rimossi i tag potenzialmente pericolosi, e lasciati solo quelli sicuri.
            // Non sono permessi tag al di fuori di quelli specificati, lasciando quindi fuori
            // script, applet, iframe e così via.
            return filter_var($input_filtered, $detected_type, $final_options);
        } else {
            // Per qualsiasi altro tipo rimasto, quindi il valore ipotizzato,
            // viene usato il filtro standard, che di per sè rimuove già tutti
            // i caratteri non utili, tag inclusi.
            return filter_input($type, $var_name, $detected_type, $final_options);
        }
    }

    public static function sanitizeJSON($json) {
        if (is_null($json)) {
            return $json;
        }

        if (is_array($json)) {
            foreach ($json as $index => $row) {
                $json[$index] = self::sanitizeJSON($row);
            }
        }

        if (is_object($json)) {
            foreach ($json as $key => $value) {
                $json->{$key} = self::sanitizeJSON($value);
            }
            return $json;
        }

        if (is_string($json)) {
            return htmlspecialchars($json);
        }

        if (is_numeric($json)) {
            return $json;
        }

        return json_encode($json);
    }

    /*
    public static function bb_encode($string)
    {
        // <b>aaa</b> --> [b]aaa[/b]

        // <a href="google.com">aaa</a> --> [a href='google.com']aaa[/a]
        // <a href='microsoft.com'>aaa</a> --> [a href='microsoft.com']aaa[/a]
        // <b>aaa</b> --> [b]aaa[/b]
        // <test>aaa</test> --> aaa

        // // // apertura [<], chiusura [>], segnalatore chiusura [/], separatore da nome valore [=], inclusione valore ["/']
        $bb_syntax = new Syntax('<', '>', '/', '=');
        $bb_handler = new HandlerContainer();

        foreach (self::$allowed_tags as $tag)
        {
            $bb_handler->add($tag, function(ShortcodeInterface $s) {
                $tag = $s->getName();
                if ($tag === 'a')
                {
                    return sprintf("[a href='%s']", $s->getParameter('href')) . $s->getContent() . '[/a]';
                }

                return '[' . $tag . ']' . $s->getContent() . '[/' . $tag . ']';
            });
        }

        $bb_processor = new Processor(new RegularParser($bb_syntax), $bb_handler);

        return $bb_processor->process($string);
    }

    public static function bb_decode($string)
    {
        // [b]aaa[/b] --> <b>aaa</b>

        // [a href="google.com"]aaa[/a] --> <a href="google.com">aaa</a>
        // [b]aaa[/b] --> <b>aaa</b>
        // [test]aaa[/test] --> aaa
        $bb_handler = new HandlerContainer();

        foreach (self::$allowed_tags as $tag)
        {
            $bb_handler->add($tag, function(ShortcodeInterface $s) {
                $tag = $s->getName();
                if ($tag === 'a')
                {
                    return sprintf("<a href='%s'>", $s->getParameter('href')) . $s->getContent() . '</a>';
                }

                return '<' . $tag . '>' . $s->getContent() . '</' . $tag . '>';
            });
        }

        $bb_processor = new Processor(new RegularParser(), $bb_handler);

        return $bb_processor->process($string);
    } */

}
