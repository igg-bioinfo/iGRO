<?php

//--------------------------------VARIABLES
$trs = '';
$cols = [];
$has_arm = class_exists('Randomization_' . Config::RAND_CLASS);


//--------------------------------NEW PATIENT
URL::changeable_vars_reset();
URL::changeable_var_add('pid', 0);
$html .= HTML::set_button(Icon::set_add() . Language::find('patient_new', ['patient']), '', URL::create_url('patient_census'));
$html .= HTML::BR;

//--------------------------------INSTRUCTIONS
//$html .= HTML::set_button(Icon::set_file() . 'PDF instructions', '', Globals::$DOMAIN_URL . Globals::$URL_RELATIVE . 'docs/instructions.pdf', '', '', '', '', 'target="_blank"');
//$html .= CRF_common::set_general_instruction_button();
//$html .= CRF_common::set_general_instruction_row();
//$html .= HTML::BR;
$html .= HTML::BR;

//--------------------------------PATIENTS SELECT
Patient_list::$filter_oCenter = $oUser->oCenter;
$oPatients = Patient_list::get_all();

//--------------------------------PATIENTS TABLE
$thead = HTML::set_tr(
    HTML::set_td(Language::find('patient_code'), '', true) .
    HTML::set_td(Language::find('name'), '', true) .
    HTML::set_td(Language::find('surname'), '', true) .
    HTML::set_td(Language::find('diagnosis'), '', true) .
    HTML::set_td(Language::find('age'), '', true) .
    ($has_arm ? HTML::set_td(Language::find('arm'), '', true) : '').
    HTML::set_td(Language::find('ongoing'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($oPatients as $oPaz) {
    URL::changeable_var_add('pid', $oPaz->id);
    $button_common = '';
    $button_common .= HTML::set_button(Icon::set_info().Language::find('patient_index'), '', URL::create_url('patient_index'));
    $bgcolor = !$oPaz->is_discontinued() && !$oPaz->has_visits() ? 'fff3cd' : ($oPaz->has_visits_not_confirmed() ? 'FFA79C' : 'd4edda');
    $button_common .= HTML::set_button(Icon::set_list().Language::find('visits').' ('.$oPaz->visits_confirmed.'/'.$oPaz->visits.')', '', URL::create_url('visits'), '', '', '', $bgcolor);
    $oRand = $has_arm ? Randomization::get_by_paz($oPaz->id) : NEW Randomization(NULL);
    //ROWS
    $trs .= HTML::set_tr(
        HTML::set_td($oPaz->patient_id) .
        HTML::set_td($oPaz->first_name) .
        HTML::set_td($oPaz->last_name) .
        HTML::set_td($oPaz->dia_short) .
        HTML::set_td($oPaz->get_age(). ' '.Language::find('years') ) .
        ($has_arm ? HTML::set_td($oRand->arm_text) : '').
        HTML::set_td(Icon::set_checker(!$oPaz->is_discontinued()) . HTML::set_spaces(2) . Date::default_to_screen($oPaz->date_end), '', false, '', 'text-align: center; ') .
        HTML::set_td($button_common) 
    );
}
$js = 'columnDefs: [
    {width: "10%", targets: [0]}, 
    {width: "", targets: [1]}, 
    {width: "", targets: [2]}, 
    {width: "10%", targets: [3]}, 
    {width: "10%", targets: [4]}, 
    {width: "10%", targets: [5]}, 
    '.($has_arm ? '{width: "10%", targets: [6]},' : '').'
    {width: "10%", targets: ['.($has_arm ? '7' : '6').']}, 
    {orderable: false, targets: ['.($has_arm ? '7' : '6').']},
    {className: "responsive-table-dynamic-column", "targets": [1,2]}
    ], '.JS::set_responsive_lang().' ';
$html .= HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'table_patients', $js);



//--------------------------------------HTML--------------------------------------//
HTML::$title = $oUser->oCenter->code.' ' . Language::find('patients');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));
