<?php

if ($oPatient->id == 0) {
    URL::redirect('error', 1);
}


//--------------------------------VARIABLES
$trs = '';
Language::add_area('visit');
$oLastVisit = new Visit();
$oLastVisit->get_last($oPatient->id);
$has_criteria = Patient_criteria::has_paz($oPatient->id);


//--------------------------------NEW VISIT
if ($oArea->id == Area::$ID_INVESTIGATOR) {
    if (!$oPatient->is_discontinued() && ($oLastVisit->id == 0 || ($oLastVisit->id != 0 && $oLastVisit->is_lock )) && $has_criteria) {
        URL::changeable_vars_reset();
        URL::changeable_var_add('pid', $oPatient->id);
        URL::changeable_var_add('vid', 0);
        $html .= HTML::set_button(Icon::set_add() . Language::find('new_visit'), '', URL::create_url('visit'));
    } else {
        $html .= HTML::BR;
        $html .= '<span class="testorosso" style="font-weight: bold;">';
        $html .= Language::find('new_visit_not_allowed').HTML::BR;
        if (!$has_criteria || $oPatient->id_end_reason == End_reason::SCREENING_FAILURE['id']) {
            $html .= '- '.Language::find('screening_failure').HTML::BR;
        } else if ($oPatient->is_discontinued()) {
            $html .= '- '.Language::find('patient_discontinued', ['patient']).$oPatient->get_general_discontinuation_detail(false).HTML::BR;
        }
        if ($oLastVisit->id != 0 && !$oLastVisit->is_lock) {
            $html .= '- '.Language::find('last_visit_not_confirmed').HTML::BR;
        }
        $html .= '</span>';
    }
    $html .= HTML::BR;
    $html .= HTML::BR;
}


//--------------------------------VISITS
$oVisits = Visit::get_all_by_id_paz($oPatient->id);
$thead = HTML::set_tr(
    HTML::set_td(Language::find('date'), '', true, '', 'width: 10px;') .
    HTML::set_td(Language::find('visit'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('locked'), '', true, '', 'width: 10px;') .
    HTML::set_td('', '', true), true
);
foreach ($oVisits as $oVis) {

    //------------VARIABLES
    $is_view = $oPatient->is_discontinued($oVis->date) || $oVis->is_lock;

    //-------------CELLS
    $tds = '';
    $tds .= HTML::set_td(Date::default_to_screen($oVis->date));
    $tds .= HTML::set_td($oVis->type_text);
    $tds .= HTML::set_td(Icon::set_checker($oVis->is_lock), '', false, '', 'text-align: center; ');

    //-------------BUTTONS
    URL::changeable_vars_reset_except(['pid']);
    URL::changeable_var_add('vid', $oVis->id);
    $buttons = '';
    if (!$is_view) {
        $buttons .= HTML::set_button(Icon::set_edit(). Language::find('modify'), '', URL::create_url('visit'));
    }
    $buttons .= HTML::set_button(Icon::set_list(). Language::find('forms'), '', URL::create_url('visit_index'));
    $tds .= HTML::set_td($buttons);
    $trs .= HTML::set_tr($tds);
}
$js = 'columnDefs: [ 
        { type: "date-dd-mmm-yyyy", targets: 0 }, 
        { width: "5px", targets: [] }, 
        { orderable: false, targets: [] } 
    ], '.JS::set_responsive_lang().' ';
$html .= HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'table_visits', $js);


//--------------------------------------HTML
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
URL::changeable_var_add('pid', $oPatient->id);
$html .= HTML::set_button(Icon::set_back() . $oPatient->patient_id .' '.Language::find('patient_index'), '', URL::create_url('patient_index'), '', 'float: left;');
HTML::$title = Language::find('visits');
HTML::$title .= '<br>' . $oPatient->patient_id .($oPatient->first_name != 'Encrypted' ? ' - '.$oPatient->first_name.' '.$oPatient->last_name : '');
HTML::print_html($html);