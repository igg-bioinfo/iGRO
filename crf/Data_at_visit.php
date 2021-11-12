<?php
class Data_at_visit extends Abstract_CRF_page {
    private $form = "";
    private $is_male = NULL;
    protected function init() {
        parent::init();
        $this->is_male = $this->oPatient->gender == '1';
    }
    function get_draw_custom_html($page_number) {
        if ($this->is_view) {
            $this->draw_page_1();
            $this->draw_page_2();
        } else {
            $this->{'draw_page_'.$page_number}();
        }
        return $this->form;
        
        return $this->form;
    }

    private function draw_page_1() {
        $oTannerGB = $this->get_draw_field_object('tanner_stage_gb');
        $oTannerPH = $this->get_draw_field_object('tanner_stage_ph');
        $this->form .= Form_input::createLabel('tanner_stage', Language::find('tanner_stage_gb'));
        for ($i = 1; $i <= 5; $i++){
            $this->form .= '<div class="form-check-inline" style="margin-left: 20px;">';
            $this->form .= Form_input::createRadioBasic($oTannerGB->name, ($this->is_male ? 'G' : 'B').$i, $oTannerGB->value, $i, "check_radio('".$oTannerGB->name."');", $this->is_view);
            $this->form .= '</div>';
            $this->form .= '<div class="form-check-inline">';
            $this->form .= Form_input::createRadioBasic($oTannerPH->name, 'PH'.$i, $oTannerPH->value, $i, "check_radio('".$oTannerPH->name."');", $this->is_view);
            $this->form .= '</div>';
            $this->form .= Form_input::br(2);
        }
        if ($this->is_male) {
            $oField = $this->get_draw_field_object('test_vol_left');
            $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_integer('".$oField->name."','','', true)", '', $this->is_view);
            $oField = $this->get_draw_field_object('test_vol_right');
            $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, true, "check_integer('".$oField->name."','','', true)", '', $this->is_view);
        }


        $this->form .= Form_input::createInputText('age', Language::find('age', ['patient']), $this->oPatient->get_age($this->oVisit->date).' '.Language::find('years'), 2, false, '', '', true);
        $oField = $this->get_draw_field_object('weight');
        $kg = $oField->value;
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_number('".$oField->name."', true)", '', $this->is_view);
        $oField = $this->get_draw_field_object('height');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_number('".$oField->name."', true)", '', $this->is_view);
        $bmi = Score::BMI($kg, $oField->value);
        if (isset($bmi)) {
            $this->form .= Form_input::createInputText('bmi', 'BMI', $bmi, 2, true, "", '', true);
        } else {
            $this->form .= Form_input::br(2);
        }

        $oField = $this->get_draw_field_object('zscore');
        $this->form .= HTML::set_td(Form_input::createInputText($oField->name, 'Z-score', $oField->value, 2, false, "check_number_unsigned('".$oField->name."', false, false, true, true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('centile');
        $this->form .= HTML::set_td(Form_input::createInputText($oField->name, 'Centile', $oField->value, 2, false, "check_number('".$oField->name."',true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('calc_method');
        $this->form .= Form_input::createLabel($oField->name, Language::find('calc_method', 'enrollment'));
        $this->form .= Form_input::createRadio($oField->name, Language::find('classic'), $oField->value, 1, 2, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, 'LMS', $oField->value, 0, 2, true, "check_radio('".$oField->name."');", $this->is_view);

        $oField = $this->get_draw_field_object('ref_last_level_specify');
        $ref_last_other = Form_input::createInputText($oField->name, Language::find('other_specify'), $oField->value, 4, true,
            JS::call_func('check_text', [$oField->name, '2', '50']), 100, $this->is_view);
        $ref_last_val = JS::create_validate_and_specify_check_call('ref_last_level', false, [$oField->name], ["99"]);
        $oField = $this->get_draw_field_object('ref_last_level');
        $this->form .= Form_input::createLabel($oField->name, Language::find('ref_last_level'));
        $this->form .= Form_input::createRadio($oField->name, 'Tanner 1966', $oField->value, 1, 6, true, "check_radio('".$oField->name."'); ".$ref_last_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, 'Cacciari 2002', $oField->value, 2, 6, true, "check_radio('".$oField->name."'); ".$ref_last_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, 'Cacciari 2006', $oField->value, 3, 6, true, "check_radio('".$oField->name."'); ".$ref_last_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, 'CDC 2009 (0-2 '.Language::find('years').')', $oField->value, 5, 6, true, "check_radio('".$oField->name."'); ".$ref_last_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, 'WHO 2007 (5-18 '.Language::find('years').')', $oField->value, 4, 6, true, "check_radio('".$oField->name."'); ".$ref_last_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('other'), $oField->value, 99, 6, true, "check_radio('".$oField->name."'); ".$ref_last_val, $this->is_view);
        $this->form .= $ref_last_other;
    }

    private function draw_page_2() {
        $oField = $this->get_draw_field_object('bone_age');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_number('".$oField->name."',true,'', true)", '', $this->is_view);

        $oField = $this->get_draw_field_object('bone_ref_specify');
        $bone_other = Form_input::createInputText($oField->name, Language::find('other_specify'), $oField->value, 4, true,
            JS::call_func('check_text', [$oField->name, '2', '50']), 100, $this->is_view);
        $bone_val = JS::create_validate_and_specify_check_call('bone_ref', false, [$oField->name], ["99"]);
        $oField = $this->get_draw_field_object('bone_ref');
        $this->form .= Form_input::createLabel($oField->name, Language::find('bone_ref'));
        $this->form .= Form_input::createRadio($oField->name, 'Atlante Greulich & Pyle', $oField->value, 1, 6, true, "check_radio('".$oField->name."'); ".$bone_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, 'TW3', $oField->value, 2, 6, true, "check_radio('".$oField->name."'); ".$bone_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('bone_software'), $oField->value, 3, 6, true, "check_radio('".$oField->name."'); ".$bone_val, $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('other'), $oField->value, 99, 6, true, "check_radio('".$oField->name."'); ".$bone_val, $this->is_view);
        $this->form .= $bone_other;

        $this->form .= Form_input::set_normal_html(HTML::set_paragraph('IGF-1'));
        $oField = $this->get_draw_field_object('igf1_dose');
        $this->form .= HTML::set_td(Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_number('".$oField->name."',true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('igf1_zscore');
        $this->form .= HTML::set_td(Form_input::createInputText($oField->name, 'Z-score', $oField->value, 2, false, "check_number('".$oField->name."',true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('igf1_ref');
        $this->form .= HTML::set_td(Form_input::createInputText($oField->name, Language::find('bone_ref'), $oField->value, 4, true, "check_text('".$oField->name."')", '', $this->is_view));

        if ($this->oVisit->type_code != 'T0') {
            $this->form .= Form_input::br(true);
            $oPrevVis = new Visit();
            $oPrevVis->get_previous($this->oPatient->id, $this->oVisit->date, true);
            $sql = "select height FROM data_at_visit dav
                inner join visit v on v.id_visita = dav.id_visita
                where v.id_visita = ? OR v.id_visita = ? order by v.date_visit DESC ";
            $heights = Database::read($sql, [$this->oVisit->id, $oPrevVis->id]);
            if (count($heights) == 2) {
                $growth_speed = $heights[0]['height'] - $heights[1]['height'].' cm';
                $this->form .= Form_input::createInputText('growth_speed_prev', Language::find('growth_speed_prev'), $growth_speed, 4, true, "", '', true);
            }
        }
        
    }

}
