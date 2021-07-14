<?php

class URL {

    //---------------------------------VARIABLES
    private static $get_encryption = "aes";
    private static $GET_page = "pg";
    private static $GET_error = "err";
    private static $sKey = Config::KEY_URL;
    private static $error_number = '';
    private static $onload_vars = array();
    public static $onload_string = '';
    private static $changeable_vars = array();
    public static $page = '';
    public static $area_url = '';
    public static $prefix = '';

    const sep_1 = "&";
    const sep_2 = "=";

    //---------------------------------PRIVATE
    private static function string_to_array($string, $check_error) {
        $array_dec = explode(self::sep_1, $string);
        self::$changeable_vars = [];
        self::$onload_vars = [];
        for ($e = 0; $e < count($array_dec); $e++) {
            $fields_dec = explode(self::sep_2, $array_dec[$e]);
            if ($check_error && $fields_dec[0] == self::$GET_error) {
                self::$error_number = $fields_dec[1];
            } else if (isset($fields_dec[1])) {
                self::$changeable_vars[$fields_dec[0]] = $fields_dec[1];
                self::$onload_vars[$fields_dec[0]] = $fields_dec[1];
            }
        }
    }

    private static function array_to_string() {
        $var_dec = '';
        $start = true;
        foreach (self::$changeable_vars as $key => $value) {
            $var_dec .= ($start ? '' : self::sep_1) . $key . self::sep_2 . $value;
            $start = false;
        }
        return $var_dec;
    }

    //---------------------------------CONSTRUCTOR
    public static function set_vars($area_url) {
        if (Security::sanitize(INPUT_GET, self::$get_encryption) . '' != '') {
            //get temp_string with possible variable error 'err'
            $temp_string = Encryption::decrypt(Security::sanitize(INPUT_GET, self::$get_encryption), self::$sKey);
            if (!$temp_string) { 
                self::redirect('error', 2, $area_url); 
            }
            self::string_to_array($temp_string, true);
            self::$onload_string = self::array_to_string();
        }
        if (Security::sanitize(INPUT_GET, self::$GET_page) . '' != '') {
            self::$page = Security::sanitize(INPUT_GET, self::$GET_page);
        }
        self::$area_url = $area_url;
        self::$prefix = 'reg_' . self::$area_url . '_';

        if (self::$error_number != '') {
            Message::write(self::$error_number);
        }
    }

    //---------------------------------PUBLIC
    public static function get_onload_var($field) {
        return isset(self::$onload_vars[$field]) ? self::$onload_vars[$field] : '';
    }

    public static function get_error() {
        return self::$error_number;
    }

    public static function changeable_vars_reset() {
        self::$changeable_vars = [];
        //self::string_to_array(self::$onload_string, false);
    }

    public static function changeable_vars_reset_except($fields) {
        self::changeable_vars_reset();
        foreach ($fields as $field) {
            self::changeable_var_add($field, self::get_onload_var($field));
        }
    }

    public static function changeable_vars_from_onload_vars() {
        self::$changeable_vars = self::$onload_vars;
    }

    public static function DEBUG_onload_string() {
        return self::$onload_string;
    }

    public static function DEBUG_changeable_string() {
        return self::array_to_string();
    }

    public static function changeable_var_add($field, $value) {
        self::$changeable_vars[$field] = $value;
    }

    public static function changeable_var_remove($field) {
        unset(self::$changeable_vars[$field]);
    }

    public static function redirect($page_url, $error_number = '', $area_url = '') {
        $area_url = $area_url == '' ? self::$area_url : $area_url;

        if ($page_url . '' == '') {
            $page_url = self::$page;
        }
        $var_dec = self::array_to_string() . ($error_number . '' == '' ? '' : self::sep_1 . self::$GET_error . self::sep_2 . $error_number);
        if ($var_dec . '' == '') {
            header("Location: " . Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . $area_url . '/' . $page_url);
            exit;
        }
        $var_enc = Encryption::encrypt($var_dec, self::$sKey);
        $url = Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . $area_url . '/' . $page_url . '/' . $var_enc;
        header("Location: " . $url);
        exit;
    }

    public static function create_url($page_url, $area_name = '', $is_relative = false, $no_area = false) {
        $area_name = $area_name == '' ? self::$area_url : $area_name;

        $var_dec = self::array_to_string();
        if ($var_dec . '' == '') {
            return ($is_relative ? '' : Globals::$DOMAIN_URL . Globals::$URL_RELATIVE) . ($no_area ? '' : $area_name . '/') . $page_url;
        }
        $var_enc = Encryption::encrypt($var_dec, self::$sKey);
        return ($is_relative ? '' : Globals::$DOMAIN_URL . Globals::$URL_RELATIVE) . ($no_area ? '' : $area_name . '/') . $page_url . '/' . $var_enc;
    }

    public static function create_url_helper($page, $vars = [], $area_name = '', $is_relative = false) {
        self::changeable_vars_reset();
        foreach ($vars as $key => $var) {
            self::changeable_var_add($key, $var);
        }
        return self::create_url($page, $area_name, $is_relative);
    }

}
