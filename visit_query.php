<?php


//--------------------------------VARIABLES
$trs = '';
Language::add_area('visit');


//--------------------------------QUERIES
$queries = Query::get_all();
$thead = HTML::set_tr(
    HTML::set_td(Language::find('patient_code'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('visit'), '', true, '', 'width: 5%;') .
    HTML::set_td(Language::find('date'), '', true, '', 'width: 5%;') .
    HTML::set_td('Query', '', true, '', 'width: 10px;') .
    HTML::set_td('', '', true, '', 'width: 10px;') .
    HTML::set_td('', '', true, '', 'width: 5%;'), true
);
foreach ($queries as $query) {
    $oVt = $query['visit'];
    $oQuery = $query['query'];

    //-------------CELLS
    $tds = '';
    URL::changeable_var_add('pid', $oVt->id_paz);
    $tds .= HTML::set_td(HTML::set_button($oVt->patient_id, '', URL::create_url('patient_index')));
    URL::changeable_var_add('vid', $oVt->id);
    $tds .= HTML::set_td(HTML::set_button($oVt->type_text, '', URL::create_url('visit_index')));
    $tds .= HTML::set_td(Date::default_to_screen($oVt->date));
    $tds .= HTML::set_td($oQuery->description);
    $tds .= HTML::set_td($oQuery->action);
    $tds .= HTML::set_td(Date::default_to_screen($oQuery->ludati, true));
    $trs .= HTML::set_tr($tds);
}
$js = 'columnDefs: [ 
        { type: "date-dd-mmm-yyyy", targets: 2 }, 
        { width: "5px", targets: [] }, 
        { orderable: false, targets: [] } 
    ], '.JS::set_responsive_lang().' ';
$html .= HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'table_queries', $js);


//--------------------------------------HTML
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float: left;');
HTML::$title = 'Queries';
HTML::print_html($html);