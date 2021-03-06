<?php


//--------------------------------VARIABLES
$trs = '';
Language::add_area('visit');
Language::add_area('patient');


//--------------------------------PATIENT
$patients = Database::read("SELECT * FROM patient_deleted ORDER BY ludati DESC ", []);
$thead = HTML::set_tr(
    HTML::set_td(Language::find('patient_code'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('export_code'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('sex'), '', true, '', 'width: 5%;').
    HTML::set_td(Language::find('diagnosis'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('visit_first'), '', true, '', 'width: 5%;') .
    HTML::set_td('', '', true, '', 'width: 5%;'), true
);
foreach ($patients as $pt) {
    $oPt = json_decode($pt['patient_json']);
    $tds = '';
    $tds .= HTML::set_td(isset($oPt->patient_id) ? $oPt->patient_id : '-');
    $tds .= HTML::set_td(isset($oPt->export_id) ? $oPt->export_id : '-');
    $tds .= HTML::set_td($oPt->gender.'' === '1' ? Language::find('male') : ($oPt->gender.'' === '2'? Language::find('female') : Language::find('unknown')));
    $tds .= HTML::set_td(isset($oPt->dia_short) ? $oPt->dia_short : '-');
    $tds .= HTML::set_td(Date::default_to_screen($oPt->date_first_visit));
    $tds .= HTML::set_td(Date::default_to_screen($pt['ludati'], true));
    $trs .= HTML::set_tr($tds);
}
$js = 'columnDefs: [ 
        { type: "date-dd-mmm-yyyy", targets: 3 }, 
        { width: "5px", targets: [] }, 
        { orderable: false, targets: [] } 
    ], '.JS::set_responsive_lang().' ';
$html .= HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'table_pts', $js);


//--------------------------------VISIT
$trs = '';
$visits = Database::read("SELECT * FROM visit_deleted ORDER BY ludati DESC ", []);
$thead = HTML::set_tr(
    HTML::set_td(Language::find('patient_code'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('export_code'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('visit'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('date'), '', true, '', 'width: 5%;') .
    HTML::set_td('', '', true, '', 'width: 5%;'), true
);
foreach ($visits as $vt) {
    $oVt = json_decode($vt['visit_json']);
    $tds = '';
    $tds .= HTML::set_td(isset($oVt->patient_id) ? $oVt->patient_id : '-');
    $tds .= HTML::set_td(isset($oVt->export_id) ? $oVt->export_id : '-');
    $tds .= HTML::set_td(isset($oVt->type_text) ? $oVt->type_text : '-');
    $tds .= HTML::set_td(Date::default_to_screen($oVt->date));
    $tds .= HTML::set_td(Date::default_to_screen($vt['ludati'], true));
    $trs .= HTML::set_tr($tds);
}
$js = 'columnDefs: [ 
        { type: "date-dd-mmm-yyyy", targets: 3 }, 
        { width: "5px", targets: [] }, 
        { orderable: false, targets: [] } 
    ], '.JS::set_responsive_lang().' ';
$html .= HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'table_vts', $js);


//--------------------------------------HTML
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
HTML::$title = 'Subjects / visits removed';
HTML::print_html($html);