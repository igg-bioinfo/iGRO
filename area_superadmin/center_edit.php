<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$oEditCtr = new Center();
$oEditCtr->get_by_id(URL::get_onload_var('cid') == '' ? 0 : URL::get_onload_var('cid'));
$is_view = false;
$form = '';


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    if ($act == 'save') {
        $oEditCtr->code = Security::sanitize(INPUT_POST, "center_code");
        $oEditCtr->hospital = Security::sanitize(INPUT_POST, "hospital");
        $oEditCtr->id_pi = Security::sanitize(INPUT_POST, "id_pi") == '' ? 0 : Security::sanitize(INPUT_POST, "id_pi");
        if ($oEditCtr->id == 0) {
            $oEditCtr->create();
            URL::changeable_var_add('cid', $oEditCtr->id);
            URL::redirect('center');
        } else {
            $oEditCtr->update();
            URL::changeable_vars_reset();
            URL::redirect('centers');
        }
    }  else if ($act == 'delete') {
        $oEditCtr->delete();
        URL::changeable_vars_reset();
        URL::redirect('centers');
    }
}


//--------------------------------FORM--------------------------------------
$form .= Form_input::createInputText('center_code', Language::find('center'), $oEditCtr->code, 4, false, "check_text('center_code','','',4);", 255, $is_view);
$form .= Form_input::createInputText('hospital', Language::find('hospital'), $oEditCtr->hospital, 4, true, JS::call_func('check_text', ['hospital', 5]), 255, $is_view);

$users = [];
$oUsers = User::get_investigators($oEditCtr->id);
foreach($oUsers as $oUsr) {
    $users[] = [$oUsr->id, $oUsr->surname.' '.$oUsr->name]; 
}
$form .= Form_input::createSelect('id_pi', Language::find('pi'), 
    $users, $oEditCtr->id_pi, 4, false, JS::call_func('check_select', ['id_pi', false]), $is_view);

$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;


//--------------------------------CENTER PW
$session_name = URL::$prefix . 'ctr_pw';
if(isset($_SESSION[$session_name])) {
    $html .= HTML::set_bootstrap_cell(Icon::set('exclamation-triangle', 2).' '.Language::find('center_pw', ['auth'])
        .': '.$_SESSION[$session_name], 12, true, 'alert alert-info', 'text-align:center;');
    unset($_SESSION[$session_name]); 
}


//--------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($oEditCtr->id != 0) {
        $text = str_replace('{0}', '<b>'.$oEditCtr->code.'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('ctr_delete', Language::find('delete').' '.$oEditCtr->code, $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#ctr_delete').modal('show');", '', '', 'float:right;');
    }
}


//--------------------------------HTML
HTML::$title = Language::find('center').' '. $oEditCtr->code;
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('centers'), '', URL::create_url('centers'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);