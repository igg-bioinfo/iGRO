<?php
class Drug_therapy extends Abstract_CRF_page {
    private $form = "";
    private $oPrevVis = NULL;
    private $prev_date = NULL;
    private $prev_dose = NULL;
    private $prev_injection = NULL;
    private $prev_reason = NULL;
    private $cur_date = NULL;
    private $cur_dose = NULL;
    private $cur_injection = NULL;
    private $cur_reason = NULL;
    const RANGE_DAYS = 0;

    function get_draw_custom_html($page_number) {
        if ($this->is_view) {
            $this->draw_page_1();
            $this->draw_page_2();
            $this->draw_page_3();
        } else {
            $this->{'draw_page_'.$page_number}();
        }
        return $this->form;
    }

    function init() {
        parent::init();
        $this->oPrevVis = new Visit();
        $this->oPrevVis->get_previous($this->oPatient->id, $this->oVisit->date, true);
        $this->get_prev_therapy();
        HTML::add_link('crf/drug_therapy', 'js');
    }

    private function get_prev_therapy() {
        $sql = "";
        $params = [];
        if ($this->oPrevVis->type_code == 'T0') {
            $sql = "SELECT gh_date_start as date_start, gh_dose as dose, gh_injection as injection, '' as end_reason  FROM enrollment WHERE id_paz = ?";
            $params = [$this->oPatient->id];
        } else {
            $sql = "SELECT date_start, dose, injection, IFNULL(end_reason, '') as end_reason FROM drug_therapy WHERE id_visita = ?";
            $params = [$this->oPrevVis->id];
        }
        $drug = Database::read($sql, $params);
        if (count($drug) > 0) {
            $this->prev_date = Date::default_to_screen($drug[0]['date_start']);
            $this->prev_dose = $drug[0]['dose'];
            $this->prev_injection = $drug[0]['injection'];
        }
    }


    //-----------------------------------PAGES
    private function draw_page_1() {
        $yes = '';
        $yes .= Form_input::createLabel('yes_why', Language::find('yes_why'));
        foreach ($this->map_form->oFields as $oFld) {
            if ($oFld->type == Field::TYPE_BOOL_0) {
                $oField = $this->get_draw_field_object($oFld->name);
                if ($oField->name == 'no_cont_other') {
                    $oSpecify = $this->get_draw_field_object('no_cont_specify');
                    $specify = Form_input::createInputText($oSpecify->name, Language::find('other_specify'), $oSpecify->value, 4, true, "check_yes_specify();", 255, $this->is_view);
                    $yes .= Form_input::createCheckbox($oField->name, Language::find('other'), $oField->value, 2, false, "check_yes_why(); check_yes_specify();", $this->is_view);
                    $yes .= $specify;
                } else {
                    $yes .= Form_input::createCheckbox($oField->name, Language::find($oField->name), $oField->value, 12, true, "check_yes_why()", $this->is_view);
                }
            }
        }
        $oField = $this->get_draw_field_object('therapy_continuity');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 12, true, 
            "check_radio('".$oField->name."'); check_continuity();", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes1'), $oField->value, 1, 12, true, 
            "check_radio('".$oField->name."'); check_continuity();", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes2'), $oField->value,2, 12, true, 
            "check_radio('".$oField->name."'); check_continuity();", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes3'), $oField->value, 3, 12, true, 
            "check_radio('".$oField->name."'); check_continuity();", $this->is_view);
        $this->form .= $yes;
    }

    private function draw_page_2() {
        $parent_check = '';
        $oField = $this->get_draw_field_object('patient_injector');
        $parent_check .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $parent_check .= Form_input::createRadio($oField->name, Language::find('yes77'), $oField->value, 1, 12, true, 
            "check_injector();", $this->is_view);
        $parent_check .= Form_input::createRadio($oField->name, Language::find('yes57'), $oField->value, 2, 12, true, 
            "check_injector();", $this->is_view);
        $parent_check .= Form_input::createRadio($oField->name, Language::find('yes17'), $oField->value, 3, 12, true, 
            "check_injector();", $this->is_view);
        $parent_check .= Form_input::createRadio($oField->name, Language::find('never'), $oField->value, 0, 12, true, 
            "check_injector();", $this->is_view);

        $oField = $this->get_draw_field_object('injector');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('parent'), $oField->value, 1, 2, false, 
            "check_radio('".$oField->name."'); check_injector();", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('patient'), $oField->value, 2, 2, false, 
            "check_radio('".$oField->name."'); check_injector();", $this->is_view);
        $this->form .= $parent_check;
    }

    private function draw_page_3() {
        $oField = $this->get_draw_field_object('date_start');
        $this->cur_date = $oField->value;
        $oField = $this->get_draw_field_object('dose');
        $this->cur_dose = $oField->value;
        $oField = $this->get_draw_field_object('injection');
        $this->cur_injection = $oField->value;
        $oField = $this->get_draw_field_object('end_reason');
        $this->cur_reason = $oField->value;
        $this->form .= Form_input::createHidden('date_start');
        $this->form .= Form_input::createHidden('dose');
        $this->form .= Form_input::createHidden('injection');
        $this->form .= Form_input::createHidden('end_reason');

        if ($this->prev_reason != '') {
            $this->form .= Form_input::createInputText('prev_reason', Language::find('therapy_ended'), $this->prev_reason, 12, true, "", '', true);
        } else {
            $this->form .= Form_input::createInputText('prev_view_dose', Language::find('gh_dose', ['enrollment']), $this->prev_dose, 2, false, "", '', true);
            $this->form .= Form_input::createInputText('prev_view_injection', Language::find('gh_injection'), $this->prev_injection, 3, false, "", '', true);
            $this->form .= Form_input::createInputText('prev_view_date', Language::find('date'), $this->prev_date, 2, false, "", "", true);
            $this->form .= Form_input::createHidden('prev_reason', $this->prev_reason);
            $this->form .= Form_input::br(true);
        }
        $this->form .= Form_input::createHidden('prev_dose', $this->prev_dose);
        $this->form .= Form_input::createHidden('prev_injection', $this->prev_injection);
        $this->form .= Form_input::createHidden('prev_date', $this->prev_date);
        
        $div_changed = '';
        $div_changed .= '</div><div class="row" id="therapy_changed" '.($this->is_view ? '' : 'style="display: none;"').'>';
        $div_changed .= Form_input::br(true);
        if (!$this->is_view || $this->cur_dose != '')
            $div_changed .= Form_input::createInputText('new_dose', Language::find('new_dose'), $this->cur_dose, 2, false, "dose_change();", '', $this->is_view);
        if (!$this->is_view || $this->cur_injection != '')
            $div_changed .= Form_input::createInputText('new_injection', Language::find('gh_injection', ['enrollment']), $this->cur_injection, 3, true, "injection_change();", '', $this->is_view);
        $date_min = $this->prev_date;
        $date_max = Date::add_days($this->oVisit->date, self::RANGE_DAYS);
        $date_max = Date::default_to_screen($date_max);
        $div_changed .= Form_input::createDatePicker('new_date_start', Language::find('date'), $this->cur_date, 2, false, "date_change('".$date_min."', '".$date_max."');", $this->is_view);
        if (!$this->is_view || $this->cur_reason != '')
            $div_changed .= Form_input::createInputText('new_reason', Language::find('stop_reason'), $this->cur_reason, 6, true, "reason_change();", '', $this->is_view);
        $div_changed .= '</div><div class="row">';

        $oField = $this->get_draw_field_object('therapy_change');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 2, false, 
            "check_radio('".$oField->name."'); check_changed(); ", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('therapy_up'), $oField->value, 1, 3, false, 
            "check_radio('".$oField->name."'); check_changed(); ", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('therapy_down'), $oField->value, 2, 3, false, 
            "check_radio('".$oField->name."'); check_changed(); ", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('therapy_ended'), $oField->value, 3, 3, true, 
            "check_radio('".$oField->name."'); check_changed(); ", $this->is_view);
        $this->form .= $div_changed;
        HTML::$js .= ' var DOSE_UP = "'.Language::find('dose_up').'"; ';
        HTML::$js .= ' var DOSE_DOWN = "'.Language::find('dose_down').'"; ';
        if (isset($this->cur_date)) {
            HTML::$js_onload .= "
            $('#new_date_start').val('".Date::default_to_screen($this->cur_date)."');
            $('#new_dose').val('".$this->cur_dose."');
            $('#new_injection').val('".$this->cur_injection."');
            $('#new_reason').val('".$this->cur_reason."');
            $('#date_start').val('".(isset($this->cur_date) ? Date::default_to_screen($this->cur_date) : $this->prev_date)."');
            $('#dose').val('".(isset($this->cur_dose) ?? $this->prev_dose)."');
            $('#injection').val('".(isset($this->cur_injection) ?? $this->prev_injection)."');
            $('#reason').val('".(isset($this->cur_reason) ?? $this->prev_reason)."'); ";
            if (!in_array($oField->value, ['0'])) {
                HTML::$js_onload .= " check_date('new_date_start', true); ";
            }
            if (!in_array($oField->value, ['0','3'])) {
                HTML::$js_onload .= " check_number('new_dose', true); ";
                HTML::$js_onload .= " check_integer('new_injection','','', true); ";
            }
            if (in_array($oField->value, ['3'])) {
                HTML::$js_onload .= " check_number('new_dose', true); ";
                HTML::$js_onload .= " check_text('new_reason','','','', true); ";
            }
        }
        $oField = $this->get_draw_field_object('therapy_good');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes'), $oField->value, 1, 2, false, 
            "check_radio('".$oField->name."'); ", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 2, false, 
            "check_radio('".$oField->name."'); ", $this->is_view);
    }
}
