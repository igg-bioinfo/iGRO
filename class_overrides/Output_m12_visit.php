<?php

class Output_m12_visit extends Output {
    public $need_check = false;
    public $has_output = true;

    //-----------------------------------------------------OVERIDDEN METHODS-----------------------------------------------------

    function calculate($get_all_text = true) {
        $this->result = '';
        $this->result .= $this->oRandomization->get_text();
    }

    protected function get_oRandomization() {
        global $oPatient;
        $class_rand = 'Randomization_' . $this->oVisit->type;
        if (class_exists($class_rand)) {
            $this->oRandomization = new $class_rand($this->oVisit);
            $this->oRandomization->center = $oPatient->oCenter->code;
            $this->oRandomization->id_dia = $oPatient->dia_short;
            $this->oRandomization->sex = $oPatient->gender;
            $this->oRandomization->get();
            $this->oRandomization->update();
        }
    }
}