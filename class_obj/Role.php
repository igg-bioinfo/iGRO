<?php

class Role {
    const SUPERADMIN = ['id' => 'wheel', 'name' => 'wheel', 'color' => 'ef738b'];
    const ADMIN = ['id' => 'admin', 'name' => 'admin', 'color' => 'CCCCFF'];
    const INVESTIGATOR = ['id' => 'investigator', 'name' => 'investigator', 'color' => 'ead5a8'];
    private static $list = [
        self::SUPERADMIN, self::ADMIN, self::INVESTIGATOR
    ];
    private static $select;

    public static function get_select() {
        global $oUser;
        self::$select = [];
        self::add(self::ADMIN);
        self::add(self::INVESTIGATOR);
        if ($oUser->is_superadmin()) { self::add(self::SUPERADMIN); }
        return self::$select;
    }

    public static function get_all() {
        return self::$list;
    }


    public static function get_color($role_id) {
        foreach(self::$list as $role) {
            if ($role['id'] == $role_id) {
                return '#'.$role['color'];
            }
        }
        return '';
    }

    private static function add($role) {
        self::$select[] = [$role['id'], Language::find($role['name'])];
    }
}