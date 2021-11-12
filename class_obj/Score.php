<?php
class Score {
    public static function BMI($weight, $height, $is_g = false, $is_cm = true) {
        if (isset($weight) && $weight > 0 && isset($height) && $height > 0) {
            $kg = $is_g ? $weight / 1000 : $weight;
            $m = $is_cm ? $height / 100 : $height;
            $m2 = pow($m, 2);
            $bmi = round($kg / $m2, 2);
            return $bmi;
        }
        return null;
    }
}