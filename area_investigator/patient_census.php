<?php

if ($oUser->oCenter->get_password() == '') {
    URL::changeable_var_add('mtp', 'center-pw');
    URL::redirect('login');
}


//--------------------------------VARIABLES
Language::add_area('patient');
$form = '';
$js_onload = '';
$is_view = Patient_criteria::has_paz($oPatient->id);

//--------------------------------PATIENT
$oPatient->get_by_id(URL::get_onload_var('pid') == '' ? 0 : URL::get_onload_var('pid'));
$oAuthor = new User();
$oAuthor->get_by_id($oPatient->author);


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act == 'save') {
    $oPatient->first_name = Security::sanitize(INPUT_POST, 'first_name');
    $oPatient->last_name = Security::sanitize(INPUT_POST, 'last_name');
    $oPatient->date_birth = Date::screen_to_default(Security::sanitize(INPUT_POST, 'date_birth'));
    $oPatient->country_birth = Security::sanitize(INPUT_POST, 'country_birth');
    $oPatient->country_birth_other = Security::sanitize(INPUT_POST, 'country_birth_other');
    $oPatient->gender = Security::sanitize(INPUT_POST, 'gender');
    $oPatient->id_diagnosis = Security::sanitize(INPUT_POST, 'diagnosis');
    $oPatient->date_diagnosis = Date::screen_to_default(Security::sanitize(INPUT_POST, 'date_diagnosis'));
    $oPatient->date_first_visit = Date::screen_to_default(Security::sanitize(INPUT_POST, 'date_first_visit'));
    $oPatient->author = $oUser->id;
    $oPatient->ethnicity = Security::sanitize(INPUT_POST, 'ethnicity');
    $oPatient->ethnicity_other = Security::sanitize(INPUT_POST, 'ethnicity_other');
    if ($oPatient->gender != '2' && $oPatient->id_diagnosis == '3') {
        URL::redirect('patient_census', 6668);
    }

    $oPatient->oCenter = $oUser->oCenter;
    if ($oPatient->id == 0) {
        $oPatient->census_create();
        URL::changeable_vars_reset();
        $oPatient->date_end = $oPatient->date_first_visit;
        $oPatient->id_end_reason = End_reason::SCREENING_FAILURE['id'];
        $oPatient->end_specify = NULL;
        $oPatient->update_discontinuation();
        URL::changeable_var_add('pid', $oPatient->id);
        URL::redirect('patient_index');
    } else {
        $oPatient->census_update();
        URL::changeable_vars_reset();
        URL::changeable_var_add('pid', $oPatient->id);
        URL::redirect('patient_index');
    }
} else if ($act == 'delete') {
    $oPatient->delete();
    URL::redirect('patients');
}


//--------------------------------FORM--------------------------------------
$form .= Form_input::createInputText('first_name', Language::find('patient_name'), $oPatient->first_name, 4, false, JS::call_func('check_text', ['first_name', 2]), 255, $is_view);
$form .= Form_input::createInputText('last_name', Language::find('patient_lastname'), $oPatient->last_name, 4, true, JS::call_func('check_text', ['last_name', 2]), 255, $is_view);


$form .= Form_input::createLabel('gender', Language::find('sex'));
$form .= Form_input::createRadio('gender', Language::find('male'), 
    $oPatient->gender, 1, 3, false, JS::call_func('check_radio', ['gender']), $is_view);
$form .= Form_input::createRadio('gender', Language::find('female'), 
    $oPatient->gender, 2, 3, true, JS::call_func('check_radio', ['gender']), $is_view);
$form .= Form_input::br(true);


$select_country_other = Database::read('SELECT country_other_code, country_other FROM country_other ORDER BY country_other ASC', array());
$cou_other = Form_input::createSelect('country_birth_other', Language::find('country_birth_other'), 
    $select_country_other, $oPatient->country_birth_other, 4, true, JS::call_func('check_select', ['country_birth_other']), $is_view);
$cou_val = JS::create_validate_and_specify_check_call('country_birth', false, ["country_birth_other"], ["IT"]);
$select_country = Database::read('SELECT country_code, country FROM country ORDER BY country ASC', array());
$form .= Form_input::createSelect('country_birth', Language::find('country_birth'), 
    $select_country, $oPatient->country_birth, 4, false, JS::call_func('check_select', ['country_birth']).' '.$cou_val, $is_view);
$form .= $cou_other;

$form .= Form_input::createLabel('ethnicity', Language::find('ethnicity'));
$ethn_other = Form_input::createInputText('ethnicity_other', Language::find('other_specify'), $oPatient->ethnicity_other, 4, true,
    JS::call_func('check_text', ['ethnicity_other', '2', '50']), 100, $is_view);
$ethn_val = JS::create_validate_and_specify_check_call('ethnicity', false, ["ethnicity_other"], ["99"]);
foreach(Ethnicity::get_all() as $ethn) {
    $form .= Form_input::createRadio('ethnicity', Language::find($ethn['name']), 
        $oPatient->ethnicity, $ethn['id'], 3, false, JS::call_func('check_radio', ['ethnicity']).' '.$ethn_val, $is_view);
}
$form .= $ethn_other;
$form .= Form_input::br(true);

$form .= Form_input::createDatePicker('date_birth', Language::find('date_birth'), 
    $oPatient->date_birth, 3, false, JS::call_func('check_dates', []), $is_view);
$form .= Form_input::createDatePicker('date_first_visit', Language::find('visit_first'), 
    $oPatient->date_first_visit, 3, false, JS::call_func('check_dates', []), $is_view);
$form .= Form_input::createDatePicker('date_diagnosis', Language::find('date_diagnosis'), 
    $oPatient->date_diagnosis, 3, true, JS::call_func('check_dates', []), $is_view);

$form .= Form_input::br(true);
$form .= Form_input::createLabel('diagnosis', Language::find('diagnosis'));
$oDiagnosis = Diagnosis::get_all();
foreach($oDiagnosis as $oDia) {
    $form .= Form_input::createRadio('diagnosis', $oDia->name, 
        $oPatient->id_diagnosis, $oDia->id, 3, false, JS::call_func('check_radio', ['diagnosis']), $is_view);
}
$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;


//--------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($oPatient->id != 0 && !Patient_criteria::has_paz($oPatient->id)) {
        $text = str_replace('{0}', '<b>'.$oPatient->patient_id.'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('paz_delete', Language::find('delete').' '.$oPatient->patient_id, $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#paz_delete').modal('show');", '', '', 'float:right;');
    }
    Message::write(6664, Message::TYPE_WARNING);
} else {
    Message::write(6665, Message::TYPE_WARNING);
}


//--------------------------------HTML
HTML::add_link('crf/patient_census', 'js');
HTML::$title = Language::find('patient_census').' '. $oPatient->patient_id;
HTML::set_audit_trail($oAuthor, $oPatient->ludati);
if ($oPatient->id != 0) {
    $html .= HTML::set_button(Icon::set_back() . Language::find('patient_index'), '', URL::create_url('patient_index'), '', 'float:left;');
}
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);