<?php
include_once('visit_index_common.php');
include_once('visit_index_checks.php');


//---------------------------------PAGE CHECK
if ($oPatient->id == 0 || $oVisit->id == 0) {
    URL::redirect('error', 1);
}


//---------------------------------CONSTANTS
const VISIT_FORM_TYPE = 'delete_visit_form_type';
const FORM_DISABLED = 2;
const FORM_REQUIRED = 1;
const FORM_OPTIONAL = 0;
const FORM_HIDDEN = 3;


//---------------------------------VARIABLES
$is_admin = $oArea->id == Area::$ID_ADMIN;
$is_investigator = $oArea->id == Area::$ID_INVESTIGATOR;
$oForms = Form::get_all_by_visit_type($oVisit->type_id, $oPatient->id, $oVisit->id);
$can_be_unlocked = $oVisit->can_be_unlocked();
$can_be_locked = $oVisit->can_be_locked();


//---------------------------------UNLOCK
if (Security::sanitize(INPUT_POST, 'unlock_mail_send') != '') {
    $subject = "Unlock data request for subject " . $oPatient->patient_id;
    $message = "Dear Staff," . HTML::BR . HTML::BR;
    $message .= "We need to unlock data for subject " . $oPatient->patient_id . " - visit "
        .Date::default_to_screen($oVisit->date) . " (".$oVisit->type_text.")." . HTML::BR;
    $message .= "Please complete it at your earliest convenience. " . HTML::BR . HTML::BR;
    $message .= "Request done by: " . $oUser->name . " " . $oUser->surname . HTML::BR;
    $message .= "Administrator (AUTOMATED EMAIL)";
    $oMailer = new Mailer();
    $oMailer->set_subject($subject);
    $oMailer->set_message($message);
    $oMailer->send([Mailer::get_admin()]);
    URL::redirect('visit_index', 55501);
}


//---------------------------------DELETE
if (Security::sanitize(INPUT_POST, VISIT_FORM_TYPE) != '') {
    $oDeleteForm = new Form(Security::sanitize(INPUT_POST, VISIT_FORM_TYPE), $oVisit->id);
    $oDeleteForm->delete();
    $oDeleteForm->delete_form_status();
    URL::redirect('visit_index');
}
HTML::$js .= JS::set_func('delete_data_form', "$('#" . VISIT_FORM_TYPE . "').val(del_form_type); $('#popup_delete_form').modal('show'); ", 'del_form_type');
$form_delete = '';
$form_delete .= Form_input::createHidden(VISIT_FORM_TYPE);
$text = str_replace('{0}', '<b>'.strtolower(Language::find('form_title')) .'</b>', Language::find('delete_confirmation', ['validation']));
$form_delete .= Form_input::createPopup('popup_delete_form',  Language::find('delete').' '.Language::find('form_title'), $text,  Language::find('delete'), "$('#form_delete').submit();");
$html .= HTML::set_form($form_delete, 'form_delete');



//--------------------------------------FORMS VARIABLES
$old_group = '';
$old_group_opt = '';
$is_first_optional = true;

$form_access = FORM_REQUIRED;
$is_form_inserted = false;
$is_form_completed = false;
$form_text_block = '';

$forms_uncompleted = 0;
$is_visit_completed = true;
$is_visit_locked = $oVisit->is_lock;

$trs = '';
$trs_opt = '';


//--------------------------------------FORMS TABLE
foreach ($oForms as $oForm) {
    $oForm->get_all_dependencies($oVisit->type_id, $oPatient->id, $oVisit->id);

    //VARIABLES
    $tds = '';
    $form_access = $oForm->is_required ? FORM_REQUIRED : FORM_OPTIONAL;
    $is_form_inserted = isset($oForm->author);
    $is_form_completed = $oForm->is_completed;

    //OPTIONAL CHECK REQUIRED
    if ($form_access === FORM_OPTIONAL) {
        $form_access = check_optional_required($oForm->type);
    }


    //SET VISIT UNCOMPLETED
    if (!$is_form_completed && ($form_access == FORM_REQUIRED || ($is_form_inserted && $form_access != FORM_DISABLED))) {
        $is_visit_completed = false;
        $forms_uncompleted++;
    }

    //GROUP
    if ($form_access == FORM_OPTIONAL) {
        $trs_opt .= set_group('optional', $old_group_opt);
    } else {
        $trs .= set_group($oForm->group, $old_group);
    }

    //ICON & BUTTONS
    $tds .= set_icon_and_title();
    if ($form_access == FORM_HIDDEN) {
        $tds .= HTML::set_td(Language::find('not_available'), '', false, '', 'min-width: 250px;');
    } else {
        $tds .= set_form_buttons($oForm->oParents);
    }
    if ($form_access == FORM_OPTIONAL) {
        $trs_opt .= HTML::set_tr($tds);
    } else {
        $trs .= HTML::set_tr($tds);
    }
}

$trs .= $trs_opt;
$trs .= set_confirm_row();
$trs .= set_unlockstatus_row();
if ($is_admin && Config::ADMIN_CHECK) {
    $trs .= set_check_row();
}
$html .= HTML::set_table($trs, '', '', 'table table-hover');
$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------------HTML
HTML::set_detail_block($oPatient, $oVisit);
URL::changeable_vars_reset();
URL::changeable_var_add('pid', $oPatient->id);
$html .= HTML::set_button(Icon::set_back().$oPatient->patient_id.' '.Language::find('visits'), '', URL::create_url('visits'));
$html .= HTML::set_spaces(2);
HTML::$title = Language::find('forms');
HTML::$title .= '<br>' . Date::default_to_screen($oVisit->date) . ' - ' . $oVisit->type_text;
HTML::print_html($html);