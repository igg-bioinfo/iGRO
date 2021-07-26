<?php

if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$trs = '';
$cols = [];
Language::add_area('visit');


//--------------------------------NEW VISIT TYPE
URL::changeable_vars_reset();
URL::changeable_var_add('vtid', 0);
$html .= HTML::set_button(Icon::set_add() . Language::find('add_new'), '', URL::create_url('visit_type'));
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::BR;

//--------------------------------VISIT TYPES SELECT
$cols[] = ["id" => 'visit_type', "label" => Language::find('visit'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'visit_type_code', "label" => Language::find('code'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'visit_day', "label" => Language::find('day'), "type" => Field::TYPE_INT, "orderable" => true];
$cols[] = ["id" => 'visit_day_lower', "label" => Language::find('min'), "type" => Field::TYPE_INT, "orderable" => true];
$cols[] = ["id" => 'visit_day_upper', "label" => Language::find('max'), "type" => Field::TYPE_INT, "orderable" => true];
$cols[] = ["id" => 'always_show', "label" => Language::find('always_show'), "type" => Paging_table::TYPE_RADIO, "orderable" => true, 
    "values" =>  [[null, Language::find('all')], ['0', Language::find('yes')], ['1', Language::find('no')]]];
$cols[] = ["id" => 'extra_visit', "label" => Language::find('extra'), "type" => Paging_table::TYPE_RADIO, "orderable" => true, 
    "values" =>  [[null, Language::find('all')], ['0', Language::find('yes')], ['1', Language::find('no')]]];
$oPaging = new Paging_table('vt_table', $cols);
$oPaging->add_order([["visit_day", "ASC"], ["visit_type", "ASC"]]);
$visit_types = $oPaging->read('visit_type_id', 
    "SELECT F.* ", 
    " FROM (SELECT VT.*, COUNT(FVT.form_id) AS count_form
        FROM visit_type VT 
        LEFT OUTER JOIN form_visit_type FVT ON FVT.visit_type_id = VT.visit_type_id
        GROUP BY VT.visit_type_id
    ) F ", 
    "", []);


//--------------------------------VISIT TYPES TABLE
$thead = HTML::set_tr(
    HTML::set_td(Language::find('name'), '', true) .
    HTML::set_td(Language::find('code'), '', true) .
    HTML::set_td(Language::find('day'), '', true) .
    HTML::set_td(Language::find('min'), '', true) .
    HTML::set_td(Language::find('max'), '', true) .
    HTML::set_td(Language::find('always_show'), '', true) .
    HTML::set_td(Language::find('extra_visit'), '', true) .
    HTML::set_td(Language::find('output'), '', true) .
    HTML::set_td(Language::find('randomization'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($visit_types as $vt) {
    URL::changeable_var_add('vtid', $vt['visit_type_id']);
    $color_f = $vt['count_form'].'' == '0' ? 'FFA79C' : '';
    $button_common = '';
    $button_common .= HTML::set_button(Icon::set_edit().Language::find('modify'), '', URL::create_url('visit_type'));
    $button_common .= HTML::set_button(Icon::set_list().Language::find('forms').' ('.$vt['count_form'].')', '', URL::create_url('visit_forms'), '', '', '', $color_f);
    $trs .= HTML::set_tr(
        HTML::set_td(Language::find($vt['visit_type'])) .
        HTML::set_td($vt['visit_type_code']) .
        HTML::set_td($vt['visit_day']) .
        HTML::set_td($vt['visit_day_lower']) .
        HTML::set_td($vt['visit_day_upper']) .
        HTML::set_td(Icon::set_checker($vt['always_show'].'' == '1')) .
        HTML::set_td(Icon::set_checker($vt['is_extra'].'' == '1')) .
        HTML::set_td(Icon::set_checker($vt['has_output'].'' == '1')) .
        HTML::set_td(Icon::set_checker($vt['has_randomization'].'' == '1')) .
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
HTML::$title = Language::find('visits');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));