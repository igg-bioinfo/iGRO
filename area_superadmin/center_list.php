<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$trs = '';
$cols = [];


//--------------------------------NEW CENTER
URL::changeable_vars_reset();
URL::changeable_var_add('cid', 0);
$html .= HTML::set_button(Icon::set_add() . Language::find('add_new'), '', URL::create_url('center'));
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------CENTERS SELECT
$cols[] = ["id" => 'center_code', "label" => Language::find('center'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'hospital', "label" => Language::find('hospital'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'pi_surname', "label" => Language::find('pi'), "type" => Field::TYPE_STRING, "orderable" => true];
$oPaging = new Paging_table('center_table', $cols);
$oPaging->add_order([["center_code", "ASC"]]);
$centers = $oPaging->read('id_center', "SELECT C.* ", " FROM ".Center::get_table()." C ", "", []);
$oCenters = [];
foreach($centers as $ctr){
    $oCenters[] = new Center($ctr);
}


//--------------------------------CENTERS TABLE
$thead = HTML::set_tr(
    HTML::set_td(Language::find('center'), '', true) .
    HTML::set_td(Language::find('hospital'), '', true) .
    HTML::set_td(Language::find('pi'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($oCenters as $oCtr) {
    URL::changeable_var_add('cid', $oCtr->id);
    $button_common = '';
    $button_common .= HTML::set_button(Icon::set_edit().Language::find('modify'), '', URL::create_url('center'));
    $trs .= HTML::set_tr(
        HTML::set_td($oCtr->code) .
        HTML::set_td($oCtr->hospital) .
        HTML::set_td($oCtr->pi_name.' '.$oCtr->pi_surname) .
        HTML::set_td($button_common) 
    );
}

$js = ' language: {
        "emptyTable": "' . Language::find('no_row_found') . '",
        search: "' . Language::find('search') . '",
        lengthMenu: "' . Language::find('DT_lengthMenu') . '",
        info: "' . Language::find('DT_info') . '",
        paginate: {
            next: "' . Language::find('next') . '",
            previous: "' . Language::find('previous') . '"
        }
    } ';
$html .= $oPaging->set($thead . HTML::set_tbody($trs), $js);


//--------------------------------------HTML
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('home'), '', URL::create_url('home'), '', 'float:left;');
HTML::$title = Language::find('centers');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));
