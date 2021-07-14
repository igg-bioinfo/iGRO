<?php

class Ethnicity {
    private static $list = [
        ['id' => 1, 'name' => 'caucasic'],
        ['id' => 2, 'name' => 'ispanic'],
        ['id' => 3, 'name' => 'afro'],
        ['id' => 4, 'name' => 'asiatic'],
        ['id' => 99, 'name' => 'other'],
    ];

    public static function get_all() {
        return self::$list;
    }
    
    public static function get_text($id) {
        foreach(self::$list as $eth) {
            if ($eth['id'] == $id) {
                return Language::find($eth['name']);
            }
        }
        return '';
    }
}