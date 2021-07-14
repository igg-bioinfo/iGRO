<?php

//--------------------------------VARIABLES
$oEditUsr = new User();
$oEditUsr->get_by_id(URL::get_onload_var('uid') == '' ? 0 : URL::get_onload_var('uid'));
$is_view = false;
$form = '';


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    if ($act == 'save') {
        $oEditUsr->name =  Security::sanitize(INPUT_POST, "name");
        $oEditUsr->surname = Security::sanitize(INPUT_POST, "surname");
        $oEditUsr->email = Security::sanitize(INPUT_POST, "email");
        $oEditUsr->phone = Security::sanitize(INPUT_POST, "phone");
        $oEditUsr->role = Security::sanitize(INPUT_POST, "role");
        $oEditUsr->id_center = Security::sanitize(INPUT_POST, "id_center") == '' ? 0 : Security::sanitize(INPUT_POST, "id_center");
        if ($oEditUsr->id == 0) {
            $oEditUsr->create();
        } else {
            $oEditUsr->update();
        }
    }  else if ($act == 'delete') {
        $oEditUsr->update_enabled(false);
    }
    URL::changeable_vars_reset();
    URL::redirect('users');
}


//--------------------------------FORM--------------------------------------
$form .= Form_input::createInputText('name', Language::find('name'), $oEditUsr->name, 4, false, JS::call_func('check_text', ['name', 2]), 255, $is_view);
$form .= Form_input::createInputText('surname', Language::find('surname'), $oEditUsr->surname, 4, true, JS::call_func('check_text', ['surname', 2]), 255, $is_view);
$form .= Form_input::createInputText('email', Language::find('email'), $oEditUsr->email, 4, false, JS::call_func('check_mail', ['email', true]), 255, $is_view);
$form .= Form_input::createInputText('phone', Language::find('phone'), $oEditUsr->phone, 4, true, JS::call_func('check_text', ['phone', '', '', '', false]), 255, $is_view);

$form .= Form_input::createSelect('role', Language::find('role'), 
    Role::get_select(), $oEditUsr->role, 4, false, JS::call_func('check_select', ['role']), $is_view);
$centers = Database::read("SELECT id_center, CONCAT(center_code, ' - ', hospital) FROM center ORDER BY center_code ASC", []);
$form .= Form_input::createSelect('id_center', Language::find('center'), 
    $centers, $oEditUsr->id_center, 4, false, JS::call_func('check_select', ['id_center', false]), $is_view);

$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;

//--------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($oEditUsr->id != 0) {
        $text = str_replace('{0}', '<b>'.$oEditUsr->email.'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('usr_delete', Language::find('delete').' '.$oEditUsr->email, $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#usr_delete').modal('show');", '', '', 'float:right;');
    }
}


//--------------------------------HTML
HTML::$title = Language::find('user').' '. $oEditUsr->email;
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('users'), '', URL::create_url('users'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);