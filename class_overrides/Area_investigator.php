<?php

class Area_investigator extends Area {

    function init_static_id() {
        self::$ID_INVESTIGATOR = $this->id;
    }
    
    function login(&$users, $username, $password) {
        $users = Database::read("SELECT * FROM " . User::get_table() . " t1 
            WHERE email = ? AND role IN ('investigator', 'wheel') AND id_center IS NOT NULL AND id_center <> 0 AND enabled = 1", [$username]);
        if (empty($users)) {
            return self::USER_NOT_EXIST;
        }
        if (!password_verify($password, $users[0]["password"])) {
            return self::WRONG_PASSWORD;
        }
        return self::LOGIN_SUCCESS;
    }
}
