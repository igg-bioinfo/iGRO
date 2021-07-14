<?php

class Error_log {

    public static $code = 0;
    public static $file = '';
    public static $line = 0;
    public static $description = '';
    public static $message = '';
    public static $id_user = 0;
    public static $id_area = 0;
    public static $oURL = NULL;

    public static function set($type, $redirect = true) {
        $sql_err_log = 'INSERT INTO error_log
            (error_code, error_file, error_line, error_type, error_description, message, id_user, id_area, ludati)
            VALUES
            (?,?,?,?,?,?,?,?,NOW())';
        if (isset($_SESSION[URL::$prefix . 'user'])) {
            $oUser = $_SESSION[URL::$prefix . 'user'];
            self::$id_user = $oUser->id;
            self::$id_area = isset($oUser->oAccessedArea) ? $oUser->oAccessedArea->id : 999;
        }
        if (isset(self::$file) && self::$file != '') {
            self::$file = str_replace(strtolower(Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE), '', strtolower(self::$file));
        }
        $params = array(&self::$code, &self::$file, &self::$line, &$type, &self::$description, &self::$message, &self::$id_user, &self::$id_area);
        Database::edit($sql_err_log, $params);

        self::$code = 0;
        self::$description = '';
        self::$message = '';
        if ($redirect) {
            URL::redirect('error', 500);
        }
    }

    public static function get() {
        $sql_err_log = 'SELECT * FROM ERROR_LOG';
        Database::read($sql_err_log, array());
    }

}
