<?php

class Test_1 extends Abstract_CRF_page {
    private $form = '';
    function get_draw_custom_html($page_number) {
        $oField = $this->get_draw_field_object('test_field');
        $this->form .= Form_input::set_normal_html('<span style="font-weight: bold; font-size: 20px;">' . Language::find($oField->name) . '</span>');
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes'), $oField->value, 1, 3, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 3, true, "check_radio('".$oField->name."');", $this->is_view);

        $oField = $this->get_draw_field_object('prove_db');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, true,"check_integer('".$oField->name."', '', '', true);", 255, $this->is_view);

        return $this->form;
    }
}