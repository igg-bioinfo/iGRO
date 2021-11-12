<?php

class Randomization_m12_visit extends Randomization {

    public $center = NULL;
    public $id_dia = NULL;
    public $sex = NULL;


    //-----------------------------------------------------OVVERRIDEN METHODS-----------------------------------------------------
    function init() {
        $this->add_extra_field('center');
        $this->add_extra_field('id_dia');
        $this->add_extra_field('sex');
    }

    function get_text() {
        global $oVisit;
        if (isset($oVisit) && $oVisit->id == $this->oVisit->id) {
            $text = Language::find('rand1');
        } else {
            $text = Language::find('rand2');
        }
        $text = str_replace('{0}', $this->arm_text, $text);
        return $text; 
    }

    function get_arm_text() {
        if ($this->arm == 1) {
            $this->arm_text = 'A (device IGRO)';
        } else if ($this->arm == 2) {
            $this->arm_text = 'B (routine)';
        }
    }
}