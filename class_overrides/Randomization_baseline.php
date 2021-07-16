<?php

class Randomization_baseline extends Randomization {

    public $group = NULL;

    //-----------------------------------------------------OVVERRIDEN METHODS-----------------------------------------------------
    function init() {
        $this->add_extra_field('group');
    }

    function get_text() {
        global $oVisit;
        if (isset($oVisit) && $oVisit->id == $this->oVisit->id) {
            return 'The subject has been randomized in Arm ' . $this->arm . ' - Group ' . $this->group . '. ';
        } else {
            return 'The subject is in Arm ' . $this->arm . ' - Group ' . $this->group . '. ';
        }
    }

    function set_extra_values() {
        $this->group = 5;
    }

}