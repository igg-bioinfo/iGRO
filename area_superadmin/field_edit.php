<?php

if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}


//--------------------------------VARIABLES
$form_id = URL::get_onload_var('fid') == '' ? 0 : URL::get_onload_var('fid');
$field_id = URL::get_onload_var('fdid') == '' ? 0 : URL::get_onload_var('fdid');
if ($form_id == 0) { 
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}
Language::add_area('form');
$oFrm = new Form();
$oFrm->get_by_id($form_id, 0, false);
$oEditFld = new Field();
$oEditFld->get_by_id($field_id, $form_id);
$is_view = false;
$form = '';
$langs = Language::get_all();


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    if ($act == 'save') {
        $oEditFld->name = Security::sanitize(INPUT_POST, "field_name");
        $oEditFld->table_name = Security::sanitize(INPUT_POST, "table_name");
        $oEditFld->type = Security::sanitize(INPUT_POST, "field_type");
        $oEditFld->is_extra_field = Security::sanitize(INPUT_POST, "is_extra_field");
        $oEditFld->description = Security::sanitize(INPUT_POST, "description");
        $oEditFld->limit_min = Security::sanitize(INPUT_POST, "limit_min").'' == '' ? NULL : Security::sanitize(INPUT_POST, "limit_min");
        $oEditFld->limit_max = Security::sanitize(INPUT_POST, "limit_max").'' == '' ? NULL : Security::sanitize(INPUT_POST, "limit_max");

        $oEditFld->order_id = Security::sanitize(INPUT_POST, "order_id");
        $oEditFld->page_number = Security::sanitize(INPUT_POST, "page_number");
        $oEditFld->required = Security::sanitize(INPUT_POST, "required");
        
        $oEditFld->save($oFrm->id);
        foreach($langs as $lang) {
            $iso = $lang['languageiso'];
            if (Security::sanitize(INPUT_POST, $iso) != '')
                Language::save($oEditFld->name, $iso, $oEditFld->table_name, Security::sanitize(INPUT_POST, $iso));
        }
    } else if ($act == 'delete') {
    }
    URL::changeable_vars_reset();
    URL::changeable_var_add('fid', $form_id);
    URL::redirect('fields');
}

//--------------------------------FORM
$form .= Form_input::createInputText('field_name', Language::find('name'), $oEditFld->name, 3, false, "check_text('field_name','2','100');", 255, $is_view);
$form .= Form_input::createInputText('table_name', Language::find('table'), $oEditFld->table_name.'' == '' ? $oFrm->class : $oEditFld->table_name, 3, true, "check_text('table_name','2','100');", 255, $is_view);

foreach($langs as $lang) {
    $iso = $lang['languageiso'];
    $form .= Form_input::createInputText($iso, $lang['translated'], Language::get($oEditFld->name, $iso), 3, false, "check_text('".$iso."','','', '', false);", 255, $is_view);
}
$form .= '</div><div class="row">';

$form .= Form_input::createSelect('field_type', Language::find('type'), Field::get_types(), $oEditFld->type, 2, false, "check_select('field_type');", $is_view);
$form .= Form_input::createInputText('limit_min', Language::find('min'), $oEditFld->limit_min, 2, false, "check_integer('limit_min');", 255, $is_view);
$form .= Form_input::createInputText('limit_max', Language::find('max'), $oEditFld->limit_max, 2, true, "check_integer('limit_max');", 255, $is_view);
$form .= Form_input::createInputText('description', Language::find('note'), $oEditFld->description, 6, true, "check_text('description','','', '', false);", 255, $is_view);
$form .= Form_input::createLabel('is_extra_field', Language::find('extra'));
$form .= Form_input::createRadio('is_extra_field', Language::find('yes'), !isset($oEditFld->is_extra_field) ? NULL :($oEditFld->is_extra_field ? 1 : 0), 1, 3, false, "check_radio('is_extra_field');", $is_view);
$form .= Form_input::createRadio('is_extra_field', Language::find('no'), !isset($oEditFld->is_extra_field) ? NULL :($oEditFld->is_extra_field ? 1 : 0), 0, 3, false, "check_radio('is_extra_field');", $is_view);

$form .= '</div><div class="row"><div class="col-lg-12 col-md-12">';
$form .= Html::set_paragraph(Language::find('form_title').' '.$oFrm->get_title());
$form .= '</div></div><div class="row">';
$form .= Form_input::createInputText('order_id', Language::find('order'), $oEditFld->order_id, 2, false, "check_integer('order_id','','', true);", 255, $is_view);
$form .= Form_input::createInputText('page_number', Language::find('page'), $oEditFld->page_number, 2, true, "check_integer('page_number','','', true);", 255, $is_view);
$form .= Form_input::createLabel('required', Language::find('required'));
$form .= Form_input::createRadio('required', Language::find('yes'), $oEditFld->required.'' == '' ? NULL :$oEditFld->required, 1, 3, false, "check_radio('required');", $is_view);
$form .= Form_input::createRadio('required', Language::find('no'), $oEditFld->required.'' == '' ? NULL :$oEditFld->required, 0, 3, false, "check_radio('required');", $is_view);

$form .= Form_input::br(true);

$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;


//--------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($oEditFld->id != 0) {
        $text = str_replace('{0}', '<b>'.$oEditFld->name.'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('fld_delete', Language::find('delete').' '.$oEditFld->name, $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#fld_delete').modal('show');", '', '', 'float:right;');
    }
}


//--------------------------------HTML
HTML::$title = Language::find('field').'<br>';
if ($oEditFld->id == 0) {
    HTML::$title .= Language::find('add_new');
} else {
    HTML::$title .= $oEditFld->name;
    $translation = $oEditFld->get_name();
    HTML::$title .= $translation.'' == '' ? '' : '<br>'.$translation;
}
URL::changeable_vars_reset_except(['fid']);
$html .= HTML::set_button(Icon::set_back() . Language::find('fields'), '', URL::create_url('fields'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);