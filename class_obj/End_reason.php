<?php

class End_reason {
    const SCREENING_FAILURE = ['id' => 1, 'name' => 'screening_failure'];
    const DEATH = ['id' => 2, 'name' => 'death'];
    const CONSENT_RETIRED = ['id' => 3, 'name' => 'consent_retired'];
    const LOST_FOLLOWUP = ['id' => 4, 'name' => 'lost_followup'];
    const EA_MAJOR = ['id' => 5, 'name' => 'ea_major'];
    const TREATMENT_FAILURE = ['id' => 6, 'name' => 'treatment_failure'];
    const OTHER = ['id' => 99, 'name' => 'other'];
    private static $list = [
        self::DEATH,
        self::CONSENT_RETIRED,
        self::LOST_FOLLOWUP,
        self::EA_MAJOR,
        self::TREATMENT_FAILURE,
        self::OTHER,
    ];

    private static function set_all() {
        $list_all = self::$list;
        $list_all[] = self::SCREENING_FAILURE;
        return $list_all;
    }

    public static function get_all() {
        return self::$list;
    }
    
    public static function get_text($id) {
        $name = self::get_name($id);
        return Language::find($name, ['patient']);
    }
    
    public static function get_id($name) {
        foreach(self::set_all() as $end) {
            if ($end['name'] == $name) {
                return $end['id'];
            }
        }
        return '';
    }
    
    public static function get_row_by_name($name) {
        foreach(self::set_all() as $end) {
            if ($end['name'] == $name) {
                return $end;
            }
        }
        return '';
    }
    
    public static function get_name($id) {
        foreach(self::set_all() as $end) {
            if ($end['id'] == $id) {
                return $end['name'];
            }
        }
        return '';
    }
}