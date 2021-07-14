<?php
if ($oPatient->id == 0) {
    URL::redirect('error', 1);
}

$is_investigator = $oArea->id == Area::$ID_INVESTIGATOR;
if ($is_investigator && $oUser->oCenter->get_password() == '') {
    URL::changeable_var_add('mtp', 'center-pw');
    URL::redirect('login');
}


//--------------------------------VARIABLES
Language::add_area('patient');
URL::changeable_vars_reset();
URL::changeable_var_add('pid', $oPatient->id);
$bootstrap_rows = '';
$oLastVisit = new Visit();
$oLastVisit->get_last($oPatient->id);


//--------------------------------FUNCTIONS
function get_date_age($prop_date) {
    global $oPatient;
    $text = '-';
    $date = isset($oPatient->{$prop_date}) ? $oPatient->{$prop_date} : $prop_date;
    if ($date.'' != '') {
        $text = Date::default_to_screen($date).' ('.strtolower(Language::find('age')).': '.$oPatient->get_age($date).' '.Language::find('years').')';
    }
    return $text;
}


//--------------------------------BODY
$button_census = $is_investigator ? HTML::set_button(Patient_criteria::has_paz($oPatient->id) ? Icon::set_view().Language::find('view') : Icon::set_edit().Language::find('modify'), '', URL::create_url('patient_census')) : '';
$bootstrap_rows .= HTML::set_row(
    HTML::set_bootstrap_cell('<h4 style="text-align:center;">'. Icon::set_user().' '.Language::find('patient_census').$button_census.'</h4>', 12));
$bootstrap_rows .= HTML::set_row(
    HTML::set_bootstrap_nested_cell(Language::find('patient_code'), HTML::set_row(
        HTML::set_bootstrap_cell($oPatient->patient_id, 12)), 4, true, 'border') .
    HTML::set_bootstrap_nested_cell(Language::find('fullname'), HTML::set_row(
        HTML::set_bootstrap_cell(($oArea->id == Area::$ID_INVESTIGATOR ? $oPatient->first_name . ' ' . $oPatient->last_name : 'Encrypted'), 12)), 4, true, 'border') .
    HTML::set_bootstrap_nested_cell(Language::find('ethnicity'), HTML::set_row(
        HTML::set_bootstrap_cell($oPatient->ethnicity_other.'' != '' ? $oPatient->ethnicity_other : Ethnicity::get_text($oPatient->ethnicity), 12)), 4, true, 'border')
);
$gender = $oPatient->gender.'' === '1' ? Language::find('male') : ($oPatient->gender.'' === '2'? Language::find('female') : Language::find('unknown'));
$bootstrap_rows .= HTML::set_row(
    HTML::set_bootstrap_nested_cell(Language::find('sex'), HTML::set_row(
        HTML::set_bootstrap_cell($gender, 12)), 4, true, 'border') .
    HTML::set_bootstrap_nested_cell(Language::find('date_birth'), HTML::set_row(
        HTML::set_bootstrap_cell(($oArea->id == Area::$ID_INVESTIGATOR ? Date::default_to_screen($oPatient->date_birth) : 'Encrypted'), 12)), 4, true, 'border') .
    HTML::set_bootstrap_nested_cell(Language::find('date_diagnosis'), 
        HTML::set_row(HTML::set_bootstrap_cell(get_date_age('date_diagnosis'), 12)), 4, true, 'border')
);
$bootstrap_rows .= HTML::set_row(
    HTML::set_bootstrap_nested_cell(Language::find('date_onset'), 
        HTML::set_row(HTML::set_bootstrap_cell(get_date_age('date_onset'), 12)), 4, true, 'border') .
    HTML::set_bootstrap_nested_cell(Language::find('visit_first'), 
        HTML::set_row(HTML::set_bootstrap_cell(get_date_age('date_first_visit'), 12)), 4, true, 'border') .
    HTML::set_bootstrap_nested_cell(Language::find('visit_last'), 
        HTML::set_row(HTML::set_bootstrap_cell(get_date_age($oLastVisit->date), 12)), 4, true, 'border')
);


//DIAGNOSIS
$bootstrap_rows .= HTML::BR;
$diagnosis = str_replace('%%%', $oPatient->dia_name, Language::find('patient_diagnosis'));
$diagnosis .= HTML::set_button(Icon::set_list().Language::find('patient_criteria'), '', URL::create_url('patient_criteria'));
$bootstrap_rows .= HTML::set_row(HTML::set_bootstrap_cell(HTML::set_text($diagnosis, true), 12, true, 'alert alert-'.(Patient_criteria::has_paz($oPatient->id) ? 'info' : 'danger'), 'text-align:center;'));
$bootstrap_rows .= HTML::BR;

//PATIENT GENERAL STATUS
$status_message = '';
if ($oPatient->is_discontinued()) {
    if ($oPatient->id_end_reason == End_reason::SCREENING_FAILURE['id']) {
        $status_message .= $oPatient->end_reason_txt;
    } else {
        $status_message .= Language::find('patient_discontinued');
        $status_message .= $oPatient->get_general_discontinuation_detail();
        $status_message .= HTML::set_button(Icon::set_edit().Language::find('end_form'), '', URL::create_url('patient_status'));
    }
} else {
    $status_message = Language::find('patient_followed');
    if (!$oPatient->has_visits_not_confirmed()) {
        $status_message .= HTML::set_button(Icon::set_edit().Language::find('end_form'), '', URL::create_url('patient_status'));
    }
}
$col = HTML::set_text($status_message, true);
$bootstrap_rows .= HTML::set_row(HTML::set_bootstrap_cell($col, 12, true, 'alert alert-'.($oPatient->is_discontinued() ? 'danger' : 'success'), 'text-align:center;'));

$html .= $bootstrap_rows;
$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------HTML
HTML::$title = Language::find('patient_index') . '<br>' . $oPatient->patient_id . 
    ($oPatient->first_name != 'Encrypted' ? ' - ' . $oPatient->first_name . ' ' . $oPatient->last_name : '');
$html .= HTML::set_button(Icon::set_back() . $oPatient->patient_id . ' ' . Language::find('visits'), '', URL::create_url('visits'), '', 'float: left;');
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
HTML::print_html($html);

