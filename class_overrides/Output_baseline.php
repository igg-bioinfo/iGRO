<?php

class Output_baseline extends Output {
    public $need_check = true;

    //-----------------------------------------------------OVERIDDEN METHODS-----------------------------------------------------
    public function init() {
        $this->has_output = true;
        $this->add_extra_field('eos', Language::find('end_reason'));
    }

    public function after_construct() {
    }
    /*
    public function draw_extra_field_inputs() {
        $html = '';
        return $html;
    }
    */

    function calculate($get_all_text = true) {
        $this->result = '';
        $this->result .= $this->get_label($this->eos, 'eos');
        $this->result .= HTML::set_br(2);
        $this->result .= $this->oRandomization->get_text();
    }
}