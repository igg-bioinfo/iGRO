<?php

if (!isset($oVisit) || !$oVisit->is_lock) {
    URL::redirect('error', 1);
}



//--------------------------------------------------SCORES TESTER--------------------------------------------------
//ACR::init();
//Abstract_score::calculate_and_save_all($oVisit->id, $oVisit->id_paz, $oVisitProjects);
//JADAS::init();
//Abstract_score::calculate_and_save_all($oVisit->id, $oVisit->id_paz, $oVisitProjects);
//JADAS_disease_activity::init();
//Abstract_score::calculate_and_save_all($oVisit->id, $oVisit->id_paz, $oVisitProjects);
//JADAS_change::init();
//Abstract_score::calculate_and_save_all($oVisit->id, $oVisit->id_paz, $oVisitProjectsNoExtra);
//Disease_activity::init();
//Abstract_score::calculate_and_save_all($oVisit->id, $oVisit->id_paz, $oVisitProjects);
//JADAS_clinical_remission::init();
//Abstract_score::calculate_and_save_all($oVisit->id, $oVisit->id_paz, $oVisitProjects);
//--------------------------------VISIT ENROLLS
$output_html = '';


//-------------------------RESPONSE
$oOutput = new Output($oVisit);
$class_output = 'Output_' . $oVisit->type;
if (class_exists($class_output)) {
    $oOutput = new $class_output($oVisit);
}

//----CALCULATE RESULT FOR OLD VISITS WHICH DON'T HAVE THE OUTPUT SAVED
//        if ($oOutput->result . '' == '') {
//            $oOutput->calculate();
//        }

//-------------------------NEED CHECK
$is_temp_text = $oOutput->need_check && !$oVisit->is_check;
$oOutput->calculate(!$is_temp_text);



//--------------------------------------------------OUTPUT TESTER--------------------------------------------------
//        $oOutput->calculate();
//        $oOutput->save();
//-------------------------TITLE & OUTPUT HTML
if ($oOutput->result != '') {
    $output_title = $oVisit->type_text;
    $output_html .= HTML::set_paragraph($output_title, ' background-color: #' . $oArea->color_background);
}
$output_html .= $oOutput->result;
$html .= $output_html == '' ? "<b>There is no output for this visit.</b>" : $output_html;

$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------------HTML
HTML::$title = Language::find('output');
HTML::$title .= '<br>' . Date::default_to_screen($oVisit->date) . ' - ' . $oVisit->type_text;
URL::changeable_var_add('pid', $oPatient->id);
URL::changeable_var_add('vid', $oVisit->id);
$html .= HTML::set_button(Icon::set_back() . Language::find('visit'), '', URL::create_url('visit_index'), '', 'float:left;');
URL::changeable_vars_from_onload_vars();
$html .= HTML::set_button(Icon::set_back() . Language::find('visits'), '', URL::create_url('visits'), '', 'float:left;');
$html .= HTML::set_spaces(2);
HTML::print_html($html);
