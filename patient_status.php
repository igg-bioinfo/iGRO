<?php
if ($oPatient->id == 0) {
    URL::redirect('error', 1);
}


//--------------------------------VARIABLES
Language::add_area('patient');
$form = '';
$is_discontinued = $oPatient->is_discontinued();
$is_investigator = $oArea->id == Area::$ID_INVESTIGATOR;
$has_visit_not_confirmed = $oPatient->has_visits_not_confirmed();
$is_view = !$is_investigator || $has_visit_not_confirmed;
$can_save_note = !$has_visit_not_confirmed || $is_discontinued;
$oAuthor = new User();
$oAuthor->get_by_id($oPatient->end_author);
$oLastVisit = new Visit();
$oLastVisit->get_last($oPatient->id);


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    if ($act == 'save') {
        $date_end = Date::screen_to_default(Security::sanitize(INPUT_POST, 'date_end')); 
        $oPatient->end_note = Security::sanitize(INPUT_POST, 'end_note');
        if ($date_end.'' == '') {
            $oPatient->update_end_note();
            URL::changeable_vars_reset_except(['pid']);
            URL::redirect('patient_index');
        }
        $oPatient->date_end = $date_end;
        $oPatient->id_end_reason = Security::sanitize(INPUT_POST, 'id_end_reason');
        $oPatient->end_specify = Security::sanitize(INPUT_POST, 'end_specify');
    } else if ($act == 'delete') {
        $oPatient->date_end = NULL;
        $oPatient->id_end_reason = NULL;
        $oPatient->end_specify = NULL;
        $oPatient->end_note = NULL;
    }
    $oPatient->update_discontinuation();
    URL::changeable_vars_reset_except(['pid']);
    URL::redirect('patient_index');
}


//--------------------------------FORM
$form .= Form_input::createDatePicker('date_end', Language::find('end_date'),  $oPatient->date_end, 4, true, 
    "if(check_text('date_end', 11)) { check_date_min_max('date_end', true, '".Date::default_to_screen($oLastVisit->date)."'); }", $is_view);
$form .= Form_input::br(true);

$form .= Form_input::createLabel('id_end_reason', Language::find('end_reason'));
$rea_other = Form_input::createInputText('end_specify', Language::find('other_specify'), $oPatient->end_specify, 4, true,
    JS::call_func('check_text', ['end_specify', '2', '50']), 100, $is_view);
$rea_val = JS::create_validate_and_specify_check_call('id_end_reason', false, ["end_specify"], ["99"]);
foreach(End_reason::get_all() as $reason) {
    if ($reason['id'] == End_reason::SCREENING_FAILURE['id']) { continue; }
    $form .= Form_input::createRadio('id_end_reason', Language::find($reason['name']), 
        $oPatient->id_end_reason, $reason['id'], 8, true, JS::call_func('check_radio', ['id_end_reason']).' '.$rea_val, $is_view);
}
$form .= $rea_other;
$form .= Form_input::br();

$form .= Form_input::createTextarea('end_note', Language::find('note'), $oPatient->end_note, 12, 6, true,
    JS::call_func('check_text', ['end_note', '', '500', '', false]), !$can_save_note);
$form .= Form_input::br();

$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');


//--------------------------------BUTTONS
if (!$has_visit_not_confirmed || $is_discontinued) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($is_investigator && !$has_visit_not_confirmed && $is_discontinued) {
        $text = str_replace('{0}', '<b>'.Language::find('end_form').'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('disc_delete', Language::find('delete').' '.Language::find('end_form'), $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#disc_delete').modal('show');", '', '', 'float:right;');
    }
}


//--------------------------------HTML
HTML::set_audit_trail($oAuthor, $oPatient->end_ludati);
HTML::$title = Language::find('end_form') . '<br>' . $oPatient->patient_id . 
    ($oPatient->first_name != 'Encrypted' ? ' - ' . $oPatient->first_name . ' ' . $oPatient->last_name : '');
$html .= HTML::set_button(Icon::set_back() . Language::find('patient_index'), '', URL::create_url('patient_index'), '', 'float: left;');
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
$html .= HTML::BR;
if ($has_visit_not_confirmed) {
    Message::write(106);
}
HTML::print_html($html);