<?php

class Database {

    //--------------------------------DATABASE
    private static $servername = Config::DBSERVERNAME;
    private static $dbname = Config::DBNAME;
    private static $username = Config::DBUSERNAME;
    private static $password = Config::DBPASSWORD;
    private static $sql = "";
    private static $params = [];
    private static $error_code = "";
    public static $error_number = "";
    public static $error_text = "";
    public static $connection = null;
    public static $as_admin = false;

    //--------------------------------GENERIC
    public static function get_connection() {
        if (self::$connection) { self::$connection = NULL; }
        $conn_string = "mysql:dbname=" . self::$dbname . ";host=" . self::$servername . ";port=3306;charset=utf8";
        self::$connection = new PDO($conn_string, 
            self::$as_admin ? Config::DBADMIN : self::$username, self::$as_admin ? Config::DBADMINPW : self::$password);
    }

    public static function read($sql, $params, $assoc_key = '') {

        self::$error_number = '';
        self::$error_text = '';
        self::$sql = $sql;
        self::$params = $params;

        $rows = array();

        try {
            self::get_connection();
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $e->getMessage().' READ<br>';
            error_log($e->getMessage());
            echo 'Internal server error';
            exit;
        }
        $sth = NULL;
        try {
            $sth = self::$connection->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $sth->execute($params);
            $rows = [];

            while ($row = $sth->fetch(PDO::FETCH_BOTH, PDO::FETCH_ORI_NEXT)) {
                if (!empty($assoc_key)) {
                    if (!isset($row[$assoc_key])) {
                        Error_log::$code = self::$error_code = 500;
                        Error_log::$message = self::$error_text = "key doesn't exist";
                        Error_log::$description = self::$sql;
                        Error_log::set('SQL');
                    }
                    if (array_key_exists($row[$assoc_key], $rows)) {
                        Error_log::$code = self::$error_code = 501;
                        Error_log::$message = self::$error_text = "key is not primary key";
                        Error_log::$description = self::$sql;
                        Error_log::set('SQL');
                    }
                    $rows[$row[$assoc_key]] = $row;
                } else {
                    $rows[] = $row;
                }
            }
        } catch (PDOException $e) {
            Error_log::$code = self::$error_code = $e->getCode();
            Error_log::$message = self::$error_text = $e->getMessage();
            Error_log::$description = self::$sql;
            Error_log::set('SQL');
        }
        $sth = NULL;
        self::$connection = NULL;
        return $rows;
    }

    public static function edit($sql, $params, $get_id = false) {
        self::$error_number = '';
        self::$error_text = '';
        self::$sql = $sql;
        self::$params = $params;

        try {
            self::get_connection();
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo self::$sql.'<br>';
            echo json_encode(self::$params).'<br>';
            echo $e->getMessage().' EDIT<br>';
            error_log($e->getMessage());
            echo 'Internal server error';
            exit;
        }
        $sth = NULL;
        try {
            $sth = self::$connection->prepare($sql);
            $sth->execute($params);
            if ($get_id) {
                $res = self::$connection->lastInsertId(); //fetch(PDO::FETCH_NUM);
                return $res; 
            }
        } catch (PDOException $e) {
            Error_log::$code = self::$error_code = $e->getCode();
            Error_log::$message = self::$error_text = $e->getMessage();
            Error_log::$description = self::$sql;
            Error_log::set('SQL');
        }
        $sth = NULL;
        self::$connection = NULL;
    }

    public static function get_view_field_mapper() {
        $sql_fied_mapper = 'SELECT
            FD_FR.form_id, FR.form_type, FR.form_class, FR.form_title, FR.is_visit_related,
            FD_FR.order_id, FD_FR.page_number, FD_FR.required, FD.field_id, FD.field_name,
            FD.field_description, FD.field_type, FD.table_name, FD.is_extra_field, FD.limit_min, FD.limit_max
            FROM  field AS FD 
            INNER JOIN field_form AS FD_FR ON FD.field_id = FD_FR.field_id 
            INNER JOIN form AS FR ON FR.form_id = FD_FR.form_id';

        return $sql_fied_mapper;
    }

    public static function get_view_form_mapper() {
        $sql_form_mapper = 'SELECT
            FR.form_id, FR.form_type, FR.form_class, FR.form_title, FR.is_visit_related,
            FD_FR.order_id, FD_FR.page_number, FD_FR.required, FD.field_id, FD.field_name, 
            FD.field_description, FD.field_type, FD.table_name, FD.is_extra_field, FD.limit_min,
            FD.limit_max
            FROM form AS FR 
            LEFT OUTER JOIN field_form AS FD_FR ON FR.form_id = FD_FR.form_id 
            LEFT OUTER JOIN field AS FD ON FD.field_id = FD_FR.field_id';

        return $sql_form_mapper;
    }

    public static function get_sql() {
        return self::$sql;
    }

    public static function table_exists($table) {
        $params = [Config::DBNAME, $table];
        $found = self::read("SELECT * FROM information_schema.tables WHERE table_schema = ? AND table_name = ?", $params);
        return count($found) > 0;
    }

    public static function field_exists($table, $field) {
        $params = [$field];
        $found = self::read("SHOW COLUMNS FROM ".$table." LIKE ?;", $params);
        return count($found) > 0;
    }



    public static function create_form($table_name, $is_visit_related){
        Database::$as_admin = true;
        if (!Database::table_exists($table_name)) {
            $sql = "CREATE TABLE `".$table_name."` (
                `".($is_visit_related ? "id_visita" : "id_paz")."` bigint(20) NOT NULL,
                `author` bigint(20) NOT NULL,
                `ludati` datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            Database::edit($sql, []);
        }
        Database::$as_admin = false;
    }
    
}
