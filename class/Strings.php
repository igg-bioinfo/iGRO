<?php

class Strings {

    //---------------------------------------STRING MANIPOLATION

    public static function remove_last($string, $chars_to_remove) {
        return strlen($string) <= $chars_to_remove ? '' : substr($string, 0, strlen($string) - $chars_to_remove);
    }

    public static function startsWith($haystack, $needle) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    public static function endsWith($haystack, $needle) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    public static function contains($haystack, $needle) {
        $haystack = strtolower($haystack);
        $needle = strtolower($needle);
        return strpos($haystack, $needle) !== FALSE;
    }

    public static function add_zeros($intValue, $length) {
        return sprintf("%0".$length."s", $intValue);
    }

    public static function upper_first($value) {
        return ucfirst(strtolower($value));
    }

    public static function left($str, $length) {
        return substr($str, 0, $length);
    }

    public static function right($str, $length) {
        return substr($str, -$length);
    }

}

?>