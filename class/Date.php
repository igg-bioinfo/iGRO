<?php

class Date {

    const INTERVAL_YEARS = 1;
    const INTERVAL_DAYS = 2;
    const INTERVAL_HOURS = 3;
    const INTERVAL_MINS = 4;
    const INTERVAL_SECONDS = 5;

    //-----------------------------------------------------STATIC
    static function object_to_screen($date, $time = false) {
        return isset($date) && $date ? strtoupper($date->format("d-M-Y" . ($time ? " H:i:s" : ""))) : "";
    }

    static function object_to_default($date, $time = false) {
        //return isset($date) && $date ? strtoupper($date->format("Y-m-d H:i:s")) : "";
        return isset($date) && $date ? strtoupper($date->format("Y-m-d" . ($time ? " H:i:s" : ""))) : "";
    }

    static function screen_to_object($date, $time = false) {
        if (!$time && !preg_match('/^[0-9]{2}-[A-Z]{3}-[0-9]{4} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $date)) {
            $date .= ' 00:00:00';
        }
        $date_temp = $date . '' != '' ? DateTime::createFromFormat('d-M-Y H:i:s', $date) : NULL;
        return $date_temp;
    }

    static function default_to_object($date, $time = false) {
        if (!$time && !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/', $date)) {
            $date .= ' 00:00:00';
        }
        $date_temp = $date . '' != '' ? DateTime::createFromFormat('Y-m-d H:i:s', $date) : NULL;
        return $date_temp;
    }

    static function default_to_screen($date, $time = false) {
        $date = self::default_to_object($date, $time);
        return isset($date) ? self::object_to_screen($date, $time) : '';
    }

    static function screen_to_default($date, $time = false) {
        $date = self::screen_to_object($date, $time);
        return isset($date) ? self::object_to_default($date, $time) : '';
    }

    static function format_to_object($date, $format) {
        $date_temp = DateTime::createFromFormat($format, $date) ? DateTime::createFromFormat($format, $date) : NULL;
        return $date_temp;
    }

    static function date_difference_in_days($first_date, $last_date, $is_absolute = false) {
        if(!is_a($first_date, 'DateTime')) {
            $date = Date::default_to_object($first_date);
        }
        if(!is_a($last_date, 'DateTime')) {
            $date = Date::default_to_object($first_date);
        }
        $date_interval = date_diff($first_date, $last_date, $is_absolute);
        return $date_interval->format('%a');
    }

    static function date_difference($first_date, $last_date, $format, $is_absolute = false) {
        if(!is_a($first_date, 'DateTime')) {
            $date = Date::default_to_object($first_date);
        }
        if(!is_a($last_date, 'DateTime')) {
            $date = Date::default_to_object($first_date);
        }
        $result = 0;
        switch ($format) {

            case self::INTERVAL_YEARS:
                $date_interval = date_diff($first_date, $last_date, $is_absolute);
                $result = round($date_interval->format(($date_interval->format('%R') == '-' ? '%R' : '') . '%a') / 365, 0);
                break;

            case self::INTERVAL_DAYS:
                $date_interval = date_diff($first_date, $last_date, $is_absolute);
                $result = $date_interval->format(($date_interval->format('%R') == '-' ? '%R' : '') . '%a');
                break;

            case self::INTERVAL_HOURS:
                $first_date = strtotime(Date::object_to_default($first_date, true));
                $last_date = strtotime(Date::object_to_default($last_date, true));
                $result = round(($last_date - $first_date) / 60 / 60, 0);
                break;

            case self::INTERVAL_MINS:
                $first_date = strtotime(Date::object_to_default($first_date, true));
                $last_date = strtotime(Date::object_to_default($last_date, true));
                $result = round(($last_date - $first_date) / 60, 0);
                break;

            case self::INTERVAL_SECONDS:
            case self::INTERVAL_MINS:
                $first_date = strtotime(Date::object_to_default($first_date, true));
                $last_date = strtotime(Date::object_to_default($last_date, true));
                $result = ($last_date - $first_date);
                break;
        }
        return $result;
    }
}