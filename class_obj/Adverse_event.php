<?php

class Adverse_event {
    private static $ae_array = [
        [1, 'inj_red'],
        [2, 'inj_itch'],
        [3, 'local_lido'],
        [4, 'hip_issue'],
        [5, 'water_retention'],
        [6, 'sleep_apnea'],
        [7, 'headache'],
        [8, 'diabetes2'],
        [9, 'thyroid'],
        [10, 'scoliosis'],
        [99, 'other']
    ];
    private static $wr_array = [
        [1, 'wr_edema'],
        [2, 'wr_stiff'],
        [3, 'wr_bone'],
        [4, 'wr_muscle'],
        [5, 'wr_alter']
    ];

    static function get_list() {;
        $ae_select = [];
        foreach(self::$ae_array as $ae) {
            $ae[1] = Language::find($ae[1]);
            $ae_select[] = $ae;
        }
        return $ae_select;
    }

    static function get_text($id) {
        foreach(self::$ae_array as $ae) {
            if ($ae[0] == $id) {
                return Language::find($ae[1]);
            }
        }
        return '';
    }

    static function get_water_retention() {;
        return self::$wr_array;
    }
}