<?php

//--------------------------------VARIABLES
$html = '';
$form = '';
$field = 'language';


//--------------------------------POST
$post = Security::sanitize(INPUT_POST, $field);
if ($post != '') {
    $_SESSION[URL::$prefix . "iso"] = $post;
    $oUser->language = $post;
    $oUser->update();
    $_SESSION[URL::$prefix . 'user'] = $oUser;
    URL::redirect($oArea->id == Area::$ID_SUPERADMIN ? 'home' : 'patients');
}


//--------------------------------FORM
$form .= Form_input::set_normal_html('<span style="font-weight: bold; font-size: 20px;">' . Language::find($field) . '</span>');
$languages = Language::get_all();
foreach($languages as $lang) {
    $form .= Form_input::createRadio($field, $lang['translated'], Language::$iso, $lang['languageiso'], 2, false, "check_radio('".$field."');", false);
} 
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::set_button(Icon::set_save() . Language::find('modify'), "page_validation('form1');", '', '', 'float:right;');


//--------------------------------HTML
HTML::$title = Language::find('language');
URL::changeable_vars_reset();
if ($oArea->id == Area::$ID_SUPERADMIN) {
    $html .= HTML::set_button(Icon::set_back() . Language::find('home'), '', URL::create_url('home'), '', 'float: left;');
} else {
    $html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
}
$html .= HTML::BR;
HTML::print_html($html);