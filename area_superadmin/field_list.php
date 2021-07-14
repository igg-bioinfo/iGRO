<?php

if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}


//--------------------------------VARIABLES
$form_id = URL::get_onload_var('fid') == '' ? 0 : URL::get_onload_var('fid');
if ($form_id == 0) { 
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}
$trs = '';
$cols = [];
$form = '';
Language::add_area('form');
$oFrm = new Form();
$oFrm->get_by_id($form_id, 0, false);


//--------------------------------NEW FIELD
URL::changeable_var_add('fdid', 0);
$html .= HTML::set_button(Icon::set_add() . Language::find('add_new'), '', URL::create_url('field'));
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------------FIELDS LIST
$thead = HTML::set_tr(
    HTML::set_td(Language::find('name'), '', true) .
    HTML::set_td(Language::find('type'), '', true) .
    HTML::set_td(Language::find('table'), '', true) .
    HTML::set_td(Language::find('extra'), '', true) .
    HTML::set_td(Language::find('required'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($oFrm->oFields as $oFld) {
    URL::changeable_var_add('fid', $form_id);
    URL::changeable_var_add('fdid', $oFld->id);
    $buttons = '';
    $buttons .= HTML::set_button(Icon::set_remove().Language::find('delete'), "");
    $buttons .= HTML::set_button(Icon::set_edit().Language::find('modify'), "", URL::create_url('field'));
    $trs .= HTML::set_tr(
        HTML::set_td($oFld->name) .
        HTML::set_td($oFld->get_type()) .
        HTML::set_td($oFld->table_name) .
        HTML::set_td(Icon::set_checker($oFld->is_extra_field)) .
        HTML::set_td(Icon::set_checker($oFld->required.'' == '1')) .
        HTML::set_td($buttons) 
    );
}
$js = 'columnDefs: [
    {width: "5px", targets: [0]}, 
    {width: "5px", targets: [1]}, 
    {width: "5px", targets: [2]}, 
    {width: "5px", targets: [3]}, 
    {width: "5px", targets: [4]}, 
    {orderable: false, targets: [5]},
    {className: "responsive-table-dynamic-column", "targets": [0,1,2,3,4,5]}
    ], '.JS::set_responsive_lang().' ';
$form .= HTML::set_bootstrap_cell(HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'fields', $js), 12);
$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', 'style="width: 100%"');
$html .= HTML::BR;


//--------------------------------------HTML
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('forms'), '', URL::create_url('forms'), '', 'float:left;');
HTML::$title = $oFrm->get_title().'<br>'.Language::find('fields');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));