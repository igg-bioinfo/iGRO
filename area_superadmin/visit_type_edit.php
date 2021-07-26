<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
Visit_type::$visit_list_mode = false;
$oEditVT = new Visit_type();
$oEditVT->get_by_id(URL::get_onload_var('vtid') == '' ? 0 : URL::get_onload_var('vtid'));
$is_view = false;
$form = '';
Language::add_area('visit');
$langs = Language::get_all();


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    if ($act == 'save') {
        $oEditVT->name = Security::sanitize(INPUT_POST, "visit_type");
        $oEditVT->code = Security::sanitize(INPUT_POST, "visit_type_code");
        $oEditVT->day = Security::sanitize(INPUT_POST, "visit_day");
        $oEditVT->day_lower = Security::sanitize(INPUT_POST, "visit_day_lower");
        $oEditVT->day_upper = Security::sanitize(INPUT_POST, "visit_day_upper");
        $oEditVT->always_show = Security::sanitize(INPUT_POST, "always_show");
        $oEditVT->is_extra = Security::sanitize(INPUT_POST, "is_extra");
        $oEditVT->has_randomization = Security::sanitize(INPUT_POST, "has_randomization");
        $oEditVT->has_output = Security::sanitize(INPUT_POST, "has_output");
        if ($oEditVT->id == 0) {
            $oEditVT->create();
        } else {
            $oEditVT->update();
        }
        foreach($langs as $lang) {
            $iso = $lang['languageiso'];
            Language::save($oEditVT->name, $iso, 'visit', Security::sanitize(INPUT_POST, $iso));
        }
    } else if ($act == 'delete' && count($oEditVT->forms) == 0) {
        $oEditVT->delete();
    }
    URL::changeable_vars_reset();
    URL::redirect('visit_types');
}


//--------------------------------FORM
$form .= Form_input::createInputText('visit_type', Language::find('name'), $oEditVT->name, 4, false, "check_text('visit_type','2','100');", 255, $is_view);
$form .= Form_input::createInputText('visit_type_code', Language::find('code'), $oEditVT->code, 2, true, "check_text('visit_type_code','2','50');", 255, $is_view);

foreach($langs as $lang) {
    $iso = $lang['languageiso'];
    $form .= Form_input::createInputText($iso, $lang['translated'], Language::get($oEditVT->name, $iso), 3, false, "check_text('".$iso."','2','100');", 255, $is_view);
}
$form .= '</div><div class="row">';

$form .= Form_input::createInputText('visit_day', Language::find('day'), $oEditVT->day, 2, false, "check_integer('visit_day', false, false, true);", 5, $is_view);
$form .= Form_input::createInputText('visit_day_lower', Language::find('min'), $oEditVT->day_lower, 2, false, "check_integer('visit_day_lower', false, false, true);", 5, $is_view);
$form .= Form_input::createInputText('visit_day_upper', Language::find('max'), $oEditVT->day_upper, 2, true, "check_integer('visit_day_upper', false, false, true);", 5, $is_view);

$form .= Form_input::createLabel('is_extra', Language::find('extra_visit'));
$is_extra = $oEditVT->id == 0 ? NULL : ($oEditVT->is_extra ? 1 : 0);
$form .= Form_input::createRadio('is_extra', Language::find('yes'), $is_extra, 1, 3, false, "check_radio('is_extra');", $is_view);
$form .= Form_input::createRadio('is_extra', Language::find('no'), $is_extra, 0, 3, true, "check_radio('is_extra');", $is_view);

$form .= Form_input::createLabel('always_show', Language::find('always_show'));
$always_show = $oEditVT->id == 0 ? NULL : ($oEditVT->always_show ? 1 : 0);
$form .= Form_input::createRadio('always_show', Language::find('yes'), $always_show, 1, 3, false, "check_radio('always_show');", $is_view);
$form .= Form_input::createRadio('always_show', Language::find('no'), $always_show, 0, 3, true, "check_radio('always_show');", $is_view);

$form .= Form_input::createLabel('has_randomization', Language::find('randomization'));
$has_randomization = $oEditVT->id == 0 ? NULL : ($oEditVT->has_randomization ? 1 : 0);
$form .= Form_input::createRadio('has_randomization', Language::find('yes'), $has_randomization, 1, 3, false, "check_radio('has_randomization');", $is_view);
$form .= Form_input::createRadio('has_randomization', Language::find('no'), $has_randomization, 0, 3, true, "check_radio('has_randomization');", $is_view);

$form .= Form_input::createLabel('has_output', Language::find('output'));
$has_output = $oEditVT->id == 0 ? NULL : ($oEditVT->has_output ? 1 : 0);
$form .= Form_input::createRadio('has_output', Language::find('yes'), $has_output, 1, 3, false, "check_radio('has_output');", $is_view);
$form .= Form_input::createRadio('has_output', Language::find('no'), $has_output, 0, 3, true, "check_radio('has_output');", $is_view);

$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;


//--------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($oEditVT->id != 0 && count($oEditVT->forms) == 0) {
        $text = str_replace('{0}', '<b>'.$oEditVT->get_name() .'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('vt_delete', Language::find('delete').' '.$oEditVT->get_name() , $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#vt_delete').modal('show');", '', '', 'float:right;');
    }
}


//--------------------------------HTML
HTML::$title = Language::find('visit').'<br>'. $oEditVT->get_name();
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('visits'), '', URL::create_url('visit_types'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);
