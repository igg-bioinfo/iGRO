<?php

if ($oPatient->id == 0) {
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
Language::add_area('patient_criteria');
$oAllCriteria = new Patient_criteria($oPatient->id);
$end_screening = End_reason::SCREENING_FAILURE['id'];
$end_not_screening = $oPatient->is_discontinued() && $oPatient->id_end_reason != $end_screening;
$has_visit = $oPatient->has_visits();
$form = '';
$js_onload = '';
$is_view = true;
$is_area_allowed = in_array($oArea->id, [Area::$ID_INVESTIGATOR]);
if ($is_area_allowed) {
    $has_criteria = Patient_criteria::has_paz($oPatient->id);
    $is_view = $has_visit || $end_not_screening || !(!$has_criteria || ($has_criteria && URL::get_onload_var('edit').'' == 'true'));
}


//--------------------------------FUNCTIONS
function special_draw($oField, &$form) {
    $has_drawn = false;
    switch($oField->name) {
        case 'age_range':
            global $oPatient;
            $trans = Language::find($oField->name.($oPatient->id_diagnosis == '2' ? '1' : ''));
            $max_age = -1;
            $min_age = -1;
            if ($oPatient->id_diagnosis == '1') {
                $max_age = $oPatient->gender == '1' ? 9 : 8;
            } else if ($oPatient->id_diagnosis == '2') {
                $min_age = 4;
                $max_age = $oPatient->gender == '1' ? 9 : 8;
            } else if ($oPatient->id_diagnosis == 3) { 
                $max_age = 11;
            }
            $age = $oPatient->get_age($oPatient->date_first_visit);
            $value = 1;
            $trans = str_replace('{1}', $min_age.'', $trans);
            $trans = str_replace('{0}', $max_age.'', $trans);
            if ($min_age != -1) {
                $value = $age >= $min_age  ? 1 : 0;
            }
            if ($max_age != -1 && $value == 1) {
                $value = $age < $max_age ? 1 : 0;
            }
            $form .= Form_input::createLabel($oField->name, $trans.' ('.$age.' '.Language::find('years').')');
            $form .= Form_input::createRadio($oField->name, Language::find('yes'), $value, 1, 3, false, '', true);
            $form .= Form_input::createRadio($oField->name, Language::find('no'), $value, 0, 3, true, '', true);
            $form .= Form_input::createHidden($oField->name, $value);
            $form .= Form_input::br(true);
            $has_drawn = true;
            break;
    }
    if (Strings::startsWith($oField->name, 'ghd') || Strings::startsWith($oField->name, 'sga') || Strings::startsWith($oField->name, 'turner')) {
        global $oPatient;
        $has_drawn = !Strings::startsWith($oField->name, strtolower($oPatient->dia_short));
    }
    return $has_drawn;
}

function special_trans($oField) {
    $trans = '';
    switch($oField->name) {
        case 'age_range':
            global $oPatient;
            break;
        case 'tanner_stage':
            global $oPatient;
            $trans = Language::find($oField->name.($oPatient->gender == '1' ? '1' : ''));
            break;
        default:
            $trans = Language::find($oField->name);
            break;
    }
    return $trans;
}

function special_js($oField, $js) {
    switch($oField->name) {
        case 'informed_consent':
        case 'date_consent':
            $js .= " if ($('#informed_consent_1').is(':checked')) 
                { $('#date_consent').prop('disabled',false); check_date('date_consent', true); } 
                else { $('#date_consent').val(''); check_date('date_consent'); $('#date_consent').prop('disabled',true); } ";
            break;
        case 'ghd_cphd':
        case 'ghd_cphd_flaw':
            $js .= " if ($('#ghd_cphd_1').is(':checked')) 
                { $('#ghd_cphd_flaw').prop('disabled',false); check_text('ghd_cphd_flaw'); } 
                else { $('#ghd_cphd_flaw').val(''); check_text('ghd_cphd_flaw', '', '', '', false); $('#ghd_cphd_flaw').prop('disabled',true); } ";
            break;
        case 'ghd_mphd':
        case 'ghd_mphd_flaw':
            $js .= " if ($('#ghd_mphd_1').is(':checked')) 
                { $('#ghd_mphd_flaw').prop('disabled',false); check_text('ghd_mphd_flaw'); } 
                else { $('#ghd_mphd_flaw').val(''); check_text('ghd_mphd_flaw', '', '', '', false); $('#ghd_mphd_flaw').prop('disabled',true); } ";
            break;
        default:
            break;
    }
    if ($oField->type == Field::TYPE_BOOL && (Strings::startsWith($oField->name, 'ghd') || Strings::startsWith($oField->name, 'sga') || Strings::startsWith($oField->name, 'turner'))) {
        $js .= JS::call_func('check_radio', [$oField->name]);
    }
    return $js;
}


//--------------------------------FUNCTIONS
function draw_criteria($oCriteria) {
    if (count($oCriteria->oFields) == 0) { return; }
    global $form, $is_view;
    $form .= HTML::set_bootstrap_cell(HTML::set_paragraph($oCriteria->get_title()), 12, true, '', 'text-align: center;');
    foreach($oCriteria->oFields as $oField) {
        $oField = $oCriteria->get_valued_field($oField->name);
        if (special_draw($oField, $form)) { continue; }
        switch($oField->type){
            case Field::TYPE_BOOL:
                $js = $oField->required == '1' ? JS::call_func('check_radio', [$oField->name]) : '';
                $form .= Form_input::createLabel($oField->name, special_trans($oField));
                $form .= Form_input::createRadio($oField->name, Language::find('yes'), 
                    $oField->value, 1, 3, false, special_js($oField, $js), $is_view);
                $form .= Form_input::createRadio($oField->name, Language::find('no'), 
                    $oField->value, 0, 3, true, special_js($oField, $js), $is_view);
                break;
            case Field::TYPE_DATE:
                $js = JS::call_func('check_date', [$oField->name, $oField->required == '1']);
                $form .= Form_input::createDatePicker($oField->name, special_trans($oField), 
                    $oField->value, 4, true, special_js($oField, $js), $is_view);
                break;
            case Field::TYPE_STRING:
                $js = JS::call_func('check_text', [$oField->name, '', '', '', $oField->required == '1']);
                $form .= Form_input::createInputText($oField->name, special_trans($oField), 
                    $oField->value, 4, true, special_js($oField, $js), $is_view);
                break;
            case Field::TYPE_INT:
                $js = JS::call_func('check_integer', [$oField->name, '', '', $oField->required == '1']);
                $form .= Form_input::createInputText($oField->name, special_trans($oField), 
                    $oField->value, 2, true, special_js($oField, $js), $is_view);
                break;
        }
    }
}
function check_criteria($oCriteria, $correct_value) {
    foreach($oCriteria->oFields as $oField) {
        if ($oField->type == Field::TYPE_BOOL) {
            $to_check = true;
            if (Strings::startsWith($oField->name, 'ghd') || Strings::startsWith($oField->name, 'sga') || Strings::startsWith($oField->name, 'turner')) {
                global $oPatient;
                $to_check = Strings::startsWith($oField->name, strtolower($oPatient->dia_short));
                if (in_array($oField->name, ['ghd_cphd', 'ghd_mphd'])) { $to_check = false; }
            }
            if ($to_check) {
                $oField = $oCriteria->get_valued_field($oField->name);
                if ($oField->value.'' != $correct_value.'') { return false; }
            }
        }
    }
    return true;
}


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act.'' != '') {
    if ($act == 'save') {
        $oAllCriteria->save();
        $has_criteria = check_criteria($oAllCriteria->oInclusion, 1) && check_criteria($oAllCriteria->oExclusion, 0);
        if (!$oPatient->is_discontinued() && !$has_criteria) {
            $oPatient->date_end = $oPatient->date_first_visit;
            $oPatient->id_end_reason = $end_screening;
        }
        if ($oPatient->is_discontinued() && $oPatient->id_end_reason == $end_screening && $has_criteria) {
            $oPatient->date_end = NULL;
            $oPatient->id_end_reason = NULL;
        }
    } else if ($act == 'delete') {
        $oAllCriteria->delete();
        $oPatient->date_end = $oPatient->date_first_visit;
        $oPatient->id_end_reason = $end_screening;
    }
    $oPatient->end_specify = NULL;
    $oPatient->update_discontinuation();
    URL::changeable_vars_reset_except(['pid']);
    URL::redirect('patient_index');
}


//--------------------------------FORM
//$form .= Form_input::createInputText('id_dia', Language::find('diagnosis'), $oPatient->dia_name, 12, true, '', 1000, true);
draw_criteria($oAllCriteria->oInclusion);
draw_criteria($oAllCriteria->oExclusion);
draw_criteria($oAllCriteria->oExtra);
$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;


//--------------------------------BUTTONS
if ($is_area_allowed) {
    if (!$is_view) {
        $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
        if (isset($oAllCriteria->oAuthor) && $oAllCriteria->oAuthor->id != 0) {
            $text = str_replace('{0}', '<b>'.strtolower(Language::find('patient_criteria')) .'</b>', Language::find('delete_confirmation'));
            $html .= Form_input::createPopup('crit_delete', Language::find('delete'), $text, Language::find('delete'), 
                "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
            $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#crit_delete').modal('show');", '', '', 'float:right;');
        }
        Message::write(6666, Message::TYPE_WARNING);
    } else if (!$has_visit && !$end_not_screening) {
        URL::changeable_var_add('edit', 'true');
        $html .= HTML::set_button(Icon::set_edit() . Language::find('modify'), '', URL::create_url('patient_criteria'), '', 'float:right;');
        URL::changeable_vars_reset_except(['pid']);
    } else {
        Message::write(6667, Message::TYPE_WARNING);
    }
}


//--------------------------------HTML
HTML::set_detail_block($oPatient);
URL::changeable_vars_reset_except(['pid']);
HTML::$title = Language::find('patient_criteria') . '<br>' . $oPatient->patient_id . 
    ($oPatient->first_name != 'Encrypted' ? ' - ' . $oPatient->first_name . ' ' . $oPatient->last_name : '');
HTML::set_audit_trail($oAllCriteria->oAuthor, $oAllCriteria->ludati);
$html .= HTML::set_button(Icon::set_back() . Language::find('patient_index'), '', URL::create_url('patient_index'), '', 'float: left;');
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
$html .= HTML::BR;
HTML::print_html($html);