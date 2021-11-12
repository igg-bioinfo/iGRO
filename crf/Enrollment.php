<?php
class Enrollment extends Abstract_CRF_page {
    private $form = "";
    const RANGE_DAYS = 15;

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


    //-----------------------DATI DEL PAZIENTE ALLA NASCITA
    private function draw_page_1() {
        $this->form .= Form_input::set_normal_html(HTML::set_paragraph(Language::find('data_at_birth')));

        if ($this->oArea->id == Area::$ID_INVESTIGATOR) {
            $this->form .= Form_input::createInputText('birth_date', Language::find('date_birth', ['patient']), Date::default_to_screen($this->oPatient->date_birth), 2, false, '', '', true);
        }
        $this->form .= Form_input::createInputText('birth_date', Language::find('age', ['patient']), $this->oPatient->get_age($this->oVisit->date).' '.Language::find('years'), 2, false, '', '', true);

        $oField = $this->get_draw_field_object('firstborn');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes'), $oField->value, 1, 3, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 3, true, "check_radio('".$oField->name."');", $this->is_view);
         
        $this->form .= $this->draw_gestational_age();

        $oField = $this->get_draw_field_object('birth_weight');
        $g = $oField->value;
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $g, 2, false, "check_integer('".$oField->name."','','',true)", '', $this->is_view);
        $oField = $this->get_draw_field_object('birth_height');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_integer('".$oField->name."','','',true)", '', $this->is_view);
        $bmi = Score::BMI($g, $oField->value, true);
        if (isset($bmi)) {
            $this->form .= Form_input::createInputText('bmi', 'BMI', $bmi, 2, false, "", '', true);
        }
    }


    //-----------------------DATI DEI GENITORI
    private function draw_page_2() {
        $this->form .= Form_input::br(true);
        $trs = '';
        $tds = HTML::set_td(strtoupper(Language::find('father')), '', true, 'testorosso');
        $tds .= HTML::set_td(strtoupper(Language::find('mother')), '', true, 'testorosso');
        $trs .= HTML::set_tr($tds);
        $oField = $this->get_draw_field_object('father_height');
        $tds = HTML::set_td(Form_input::createInputText($oField->name, Language::find('height'), $oField->value, 4, false, "check_integer('".$oField->name."','','',true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('mother_height');
        $tds .= HTML::set_td(Form_input::createInputText($oField->name, Language::find('height'), $oField->value, 4, false, "check_integer('".$oField->name."','','',true)", '', $this->is_view));
        $trs .= HTML::set_tr($tds);
        $oField = $this->get_draw_field_object('father_zscore');
        $tds = HTML::set_td(Form_input::createInputText($oField->name, 'Z-score', $oField->value, 4, false, "check_number_unsigned('".$oField->name."', false, false, true, true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('mother_zscore');
        $tds .= HTML::set_td(Form_input::createInputText($oField->name, 'Z-score', $oField->value, 4, false, "check_number_unsigned('".$oField->name."', false, false, true, true)", '', $this->is_view));
        $trs .= HTML::set_tr($tds);
        $oField = $this->get_draw_field_object('father_centile');
        $tds = HTML::set_td(Form_input::createInputText($oField->name, 'Centile', $oField->value, 4, false, "check_number('".$oField->name."', false)", '', $this->is_view));
        $oField = $this->get_draw_field_object('mother_centile');
        $tds .= HTML::set_td(Form_input::createInputText($oField->name, 'Centile', $oField->value, 4, false, "check_number('".$oField->name."', false)", '', $this->is_view));
        $trs .= HTML::set_tr($tds);

        $oField = $this->get_draw_field_object('father_method');
        $radio = Form_input::createLabel($oField->name, Language::find('calc_method'));
        $radio .= Form_input::createRadio($oField->name, Language::find('classic'), $oField->value, 1, 3, false, "check_radio('".$oField->name."');", $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'LMS', $oField->value, 0, 3, true, "check_radio('".$oField->name."');", $this->is_view);
        $tds = HTML::set_td($radio);
        $oField = $this->get_draw_field_object('mother_method');
        $radio = Form_input::createLabel($oField->name, Language::find('calc_method'));
        $radio .= Form_input::createRadio($oField->name, Language::find('classic'), $oField->value, 1, 3, false, "check_radio('".$oField->name."');", $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'LMS', $oField->value, 0, 3, true, "check_radio('".$oField->name."');", $this->is_view);
        $tds .= HTML::set_td($radio);
        $trs .= HTML::set_tr($tds);

        $oField = $this->get_draw_field_object('father_ref_specify');
        $father_last_other = Form_input::createInputText($oField->name, Language::find('other_specify'), $oField->value, 6, true,
            JS::call_func('check_text', [$oField->name, '2', '50']), 100, $this->is_view);
        $father_last_val = JS::create_validate_and_specify_check_call('father_ref_last_level', false, [$oField->name], ["99"]);
        $oField = $this->get_draw_field_object('father_ref_last_level');
        $radio = Form_input::createLabel($oField->name, Language::find('ref_last_level'));
        $radio .= Form_input::createRadio($oField->name, 'Tanner 1966', $oField->value, 1, 6, true, "check_radio('".$oField->name."'); ".$father_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'Cacciari 2002', $oField->value, 2, 6, true, "check_radio('".$oField->name."'); ".$father_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'Cacciari 2006', $oField->value, 3, 6, true, "check_radio('".$oField->name."'); ".$father_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'WHO 2007', $oField->value, 4, 6, true, "check_radio('".$oField->name."'); ".$father_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, Language::find('other'), $oField->value, 99, 6, true, "check_radio('".$oField->name."'); ".$father_last_val, $this->is_view);
        $radio .= $father_last_other;
        $tds = HTML::set_td($radio);
        
        $oField = $this->get_draw_field_object('mother_ref_specify');
        $mother_last_other = Form_input::createInputText($oField->name, Language::find('other_specify'), $oField->value, 6, true,
            JS::call_func('check_text', [$oField->name, '2', '50']), 100, $this->is_view);
        $mother_last_val = JS::create_validate_and_specify_check_call('mother_ref_last_level', false, [$oField->name], ["99"]);
        $oField = $this->get_draw_field_object('mother_ref_last_level');
        $radio = Form_input::createLabel($oField->name, Language::find('ref_last_level'));
        $radio .= Form_input::createRadio($oField->name, 'Tanner 1966', $oField->value, 1, 6, true, "check_radio('".$oField->name."'); ".$mother_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'Cacciari 2002', $oField->value, 2, 6, true, "check_radio('".$oField->name."'); ".$mother_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'Cacciari 2006', $oField->value, 3, 6, true, "check_radio('".$oField->name."'); ".$mother_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, 'WHO 2007', $oField->value, 4, 6, true, "check_radio('".$oField->name."'); ".$mother_last_val, $this->is_view);
        $radio .= Form_input::createRadio($oField->name, Language::find('other'), $oField->value, 99, 6, true, "check_radio('".$oField->name."'); ".$mother_last_val, $this->is_view);
        $radio .= $mother_last_other;
        $tds .= HTML::set_td($radio);
        $trs .= HTML::set_tr($tds);
        $this->form .= Form_input::set_normal_html(HTML::set_table($trs, '', ''));

        $oField = $this->get_draw_field_object('genetic_target');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_number('".$oField->name."',true)", '', $this->is_view);
        $oField = $this->get_draw_field_object('genetic_target_zscore');
        $this->form .= HTML::set_td(Form_input::createInputText($oField->name, 'Z-score', $oField->value, 4, false, "check_number_unsigned('".$oField->name."', false, false, true, true)", '', $this->is_view));
        $oField = $this->get_draw_field_object('genetic_target_specify');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 6, true, "check_text('".$oField->name."')", '', $this->is_view);
    }


    //-----------------------DATI DELLA TERAPIA CON GH
    private function draw_page_3() {

        $this->form .= Form_input::set_normal_html(HTML::set_paragraph(Language::find('data_gh')));
        $oField = $this->get_draw_field_object('gh_date_start');
        $date_min = Date::default_to_screen($this->oVisit->date);
        //$this->oPatient->date_birth == 'Encrypted' ? "undefined" : "'".Date::default_to_screen($this->oPatient->date_birth)."'";
        $date_max = Date::add_days($this->oVisit->date, self::RANGE_DAYS);
        $date_max = Date::default_to_screen($date_max);
        $js = "check_date_min_max('".$oField->name."',true, '".$date_min."', '".$date_max."'); ";
        $this->form .= Form_input::createDatePicker($oField->name, Language::find($oField->name), $oField->value, 3, false, $js, $this->is_view);
        $oField = $this->get_draw_field_object('gh_dose');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, false, "check_number('".$oField->name."', true)", '', $this->is_view);
        $oField = $this->get_draw_field_object('gh_injection');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 2, true, "check_integer('".$oField->name."','5','8', true)", '', $this->is_view);
        $oField = $this->get_draw_field_object('commercial_product');
        $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 3, true, "check_text('".$oField->name."','','', '',true)", '', $this->is_view);
        if ($this->oPatient->dia_short == 'GHD') {
            $this->form .= Form_input::createLabel('gh_max_label',  Language::find('gh_max_label'));
            $oField = $this->get_draw_field_object('gh_max1');
            $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 3, false, "check_number('".$oField->name."', true)", '', $this->is_view);
            $oField = $this->get_draw_field_object('gh_test_type1');
            $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 6, true, "check_text('".$oField->name."','','', '',true)", '', $this->is_view);
            $oField = $this->get_draw_field_object('gh_max2');
            $this->form .= Form_input::createInputText($oField->name, Language::find($oField->name), $oField->value, 3, false, "check_number('".$oField->name."', true)", '', $this->is_view);
            $oField = $this->get_draw_field_object('gh_test_type2');
            $this->form .= Form_input::createInputText($oField->name, Language::find('gh_test_type1'), $oField->value, 6, true, "check_text('".$oField->name."','','', '',true)", '', $this->is_view);
        }
    }

    private function draw_gestational_age() {
        $age_text = '';
        $age_text .= Form_input::createLabel('age_label', Language::find('gestational_age'));
        $age_text .= '<div class="form-group '.Form_input::get_space_column_classes(1).'">';
        $oField = $this->get_draw_field_object('birth_weeks');
        $age_text .= Form_input::createInputTextBasic($oField->name, $oField->value, "check_integer('".$oField->name."', 0, 80, true)", 2, $this->is_view);
        $age_text .= '</div>';
        $age_text .= '<div class="form-group '.Form_input::get_space_column_classes(1).'">'.strtolower(Language::find('weeks')).' & </div>';

        $age_text .= '<div class="form-group '.Form_input::get_space_column_classes(1).'">';
        $oField = $this->get_draw_field_object('birth_days');
        $age_text .= Form_input::createInputTextBasic($oField->name, $oField->value, "check_integer('".$oField->name."', 0, 7, true)", 2, $this->is_view);
        $age_text .= '</div>';
        $age_text .= '<div class="form-group '.Form_input::get_space_column_classes(1).'">'.strtolower(Language::find('days')).'</div>';
        $age_text .= Form_input::br(true);
        return $age_text;
    }
}
