<?php

class Center {

    public $id = 0;
    public $code = '';
    public $hospital = '';
    public $id_pi = 0;
    public $pi_name = '';
    public $pi_surname = '';
    private $password = '';

    //-----------------------------------------------------CONSTRUCT & INTERFACE METHODS
    public function __construct($row = NULL) {
        if (isset($row)) {
            $this->set_by_row($row);
        }
    }

    public function get_by_id($id) {
        $centers = Database::read("SELECT C.* FROM " . self::get_table() . " C WHERE C.id_center = ?", [$id]);
        if (count($centers) > 0) {
            $this->set_by_row($centers[0]);
        }
    }

    public function set_by_row($center) {
        $this->id = $center["id_center"];
        $this->code = $center["center_code"];
        $this->hospital = $center["hospital"];
        $this->id_pi = $center["id_pi"];
        $this->pi_name = $center["pi_name"];
        $this->pi_surname = $center["pi_surname"];
    }

    //-----------------------------------------------------PUBLIC
    public function check_password($center_password) {
        $centers = Database::read("SELECT IC.* FROM " . self::get_table() . "  IC WHERE IC.center_sha_pw = ?", [hash('sha256', $center_password)]);
        if (count($centers) > 0 && $centers[0]["id_center"] == $this->id) {
            $this->password = $center_password;
        }
    }

    public function has_password() {
        return $this->password . '' != '';
    }

    public function get_password() {
        return $this->password;
    }

    public static function get_details($id) {
        $centers = Database::read("SELECT IC.* FROM " . self::get_table() . "  IC WHERE IC.id_center = ?", [$id]);
        $oCenter = new Center();
        if (count($centers) > 0) {
            $oCenter->set_by_row($centers[0]);
        }
        return $oCenter;
    }

    public static function get_all() {
        $centers = Database::read("SELECT C.* FROM " . self::get_table() . " C order by center_code ", array());
        $oCenters = [];
        foreach ($centers as $center) {
            $oCenter = new Center($center);
            $oCenters[] = $oCenter;
        }
        return $oCenters;
    }
    
    public static function get_table() {
        $sql = " ( SELECT C.id_center, center_code, C.hospital, C.id_pi, U.name as pi_name, U.surname as pi_surname, C.center_sha_pw FROM center C
            LEFT OUTER JOIN " . User::get_table(true) . " U ON U.id_user = C.id_pi ) ";
        return $sql;
    }

    function create() {
        $pw = Security::random(8);
        $sha_pw = hash('sha256', $pw);
        $_SESSION[URL::$prefix . 'ctr_pw'] = $pw;
        $params = [$this->code, $this->hospital, $this->id_pi, $sha_pw];
        $sql = "INSERT INTO center (center_code, hospital, id_pi, center_sha_pw) 
            VALUES (?, ?, ?, ?)";
        $this->id = Database::edit($sql, $params, true);
    }

    function update() {
        $params = [$this->hospital, $this->id_pi, $this->id];
        $sql = "UPDATE center SET hospital = ?, id_pi = ?  WHERE id_center = ? ";
        Database::edit($sql, $params);
    }

    function delete() {
        $params = [$this->id];
        $sql = "DELETE FROM center WHERE id_center = ? ";
        Database::edit($sql, $params);
    }

}
