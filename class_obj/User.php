<?php

class User {

    public $id = 0;
    public $oAccessedArea = NULL;
    public $ip_address = '';
    public $name = '';
    public $surname = '';
    public $email = '';
    public $role = '';
    public $phone = '';
    public $enabled = false;
    public $id_center = NULL;
    public $center = NULL;
    public $oAreas = [];
    public $oCenter = NULL;

    //-----------------------------------------------------CONSTRUCT & INTERFACE METHODS
    public function __construct($ip_address = '', $row = NULL, $oAccessedArea = NULL) {
        $this->ip_address = $ip_address;
        if (isset($row)) {
            $this->set_by_row($row);
            $this->oAccessedArea = $oAccessedArea;
        }
    }

    public function get_by_id($id) {
        $users = Database::read("SELECT * FROM " . self::get_table() . " T_Author WHERE id_user = ?", [$id]);
        if (count($users) > 0) {
            $this->set_by_row($users[0]);
        }
    }

    public function set_by_row($user) {
        $this->id = $user["id_user"];
        $this->name = $user["name"];
        $this->surname = $user["surname"];
        $this->email = $user["email"];
        $this->role = $user["role"];
        $this->phone = $user["phone"];
        $this->id_center = isset($user["id_center"]) ? $user["id_center"] : 0;
        $this->center = isset($user["center_code"]) ? $user["center_code"] : '';
        $this->enabled = $user["enabled"] == 1;
        if (isset($user["id_center"]) && isset($user["id_center"]) != 0 && (!isset($this->oCenter) || $this->oCenter->id == 0)) {
            $this->oCenter = new Center();
            $this->oCenter->get_by_id($user["id_center"]);
        }
    }

    //-----------------------------------------------------PUBLIC
    public function is_logged() {
        return $this->id != 0;
    }

    public function is_superadmin() {
        return $this->role == 'wheel';
    }

    public function check_access($oArea) {
        return (isset($this->oAccessedArea) && $this->oAccessedArea->id == $oArea->id);
    }

    public function logout() {
        foreach ($_SESSION as $key => $value) {
            if (Strings::startsWith($key, URL::$prefix)) {
                unset($_SESSION[$key]);
            }
        }
        if ($this->is_logged()) {
            $sql = "INSERT INTO session_archive (id_user, id_area, ip_address, date_login, date_logout)
            SELECT id_user, id_area, ip_address, date_login, NOW()
            FROM session WHERE id_user = ? AND id_area = ? AND ip_address = ?;

            DELETE FROM session WHERE id_user = ? AND id_area = ? AND ip_address = ?; ";
            $params = [$this->id, $this->oAccessedArea->id, $this->ip_address,
                $this->id, $this->oAccessedArea->id, $this->ip_address];
            Database::edit($sql, $params);
        }
    }

    static public function get_all_by_ids($only_ids) {
        $oPhysicians = [];
        if ($only_ids != '') {
            $sql = "SELECT DISTINCT I.* FROM " . self::get_table() . " I WHERE I.id_user IN (" . $only_ids . ") ORDER BY I.surname, I.name ";
            $params = [];
            $meds = Database::read($sql, $params);
            foreach ($meds as $med) {
                $oUser = new User('', $med);
                $oPhysicians[] = $oUser;
            }
        }
        return $oPhysicians;
    }

    static public function get_investigators($id_center = 0, $only_active = false) {
        $params = [];
        $sql = "SELECT I.* FROM " . self::get_table() . " I
            WHERE I.id_center = ?
            ORDER BY I.surname, I.name ";
        $params[] = $id_center;
        $investigators = Database::read($sql, $params);
        $oInvestigators = [];
        foreach ($investigators as $investigator) {
            $oUser = new User('', $investigator);
            $oInvestigators[] = $oUser;
        }
        return $oInvestigators;
    }
    
    public static function get_table($no_center = false) {
        $sql = " ( SELECT id_user, name, surname, email, phone, role, password, pswdate, enabled, 
            I.id_center".($no_center ? "" : ", center_code, hospital, id_pi, pi_name, pi_surname")."  
            FROM user I
            ".($no_center ? "" : "LEFT OUTER JOIN ".Center::get_table()." C ON C.id_center = I.id_center ")." 
            ) ";
        return $sql;
    }

    public static function get_by_email($email, $ip_address = '') {
        $users = Database::read("SELECT * FROM " . self::get_table() . " t1 WHERE email = ?", [$email]);
        if (count($users) > 0) {
            return new User($ip_address, $users[0]);
        }
        return null;
    }

    function create() {
        $params = [$this->name, $this->surname, $this->email, $this->phone, $this->role, $this->id_center, 1];
        $sql = "INSERT INTO user (name, surname, email, phone, role, id_center, enabled) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        Database::edit($sql, $params);
    }

    function update() {
        $params = [$this->name, $this->surname, $this->email, $this->phone, $this->role, $this->id_center, $this->id];
        $sql = "UPDATE user SET name = ?, surname = ?, email = ?, phone = ?, role = ?, id_center = ?  WHERE id_user = ? ";
        Database::edit($sql, $params);
    }

    function update_enabled($enable) {
        if (isset($this->id) && $this->id != 0) {
            $sql = "UPDATE user SET enabled = ?  WHERE id_user = ? ";
            $params = [$enable ? 1 : 0, $this->id];
            Database::edit($sql, $params);
        }
    }

}
