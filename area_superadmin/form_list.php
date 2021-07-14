<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$trs = '';
$cols = [];
Language::add_area('form');


//--------------------------------NEW FORM
URL::changeable_vars_reset();
URL::changeable_var_add('fid', 0);
$html .= HTML::set_button(Icon::set_add() . Language::find('add_new'), '', URL::create_url('form'));
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------FORMS SELECT
$cols[] = ["id" => 'form_type', "label" => Language::find('form_title'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'form_class', "label" => Language::find('class'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'form_title', "label" => Language::find('name'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'is_visit_related', "label" => Language::find('type'), "type" => Paging_table::TYPE_RADIO, "orderable" => true, 
    "values" =>  [[null, Language::find('all')], ['0', Language::find('patient')], ['1', Language::find('visit')]]];
$oPaging = new Paging_table('form_table', $cols);
$oPaging->add_order([["form_type", "ASC"], ["form_class", "ASC"]]);
$forms = $oPaging->read('form_id', 
    "SELECT F.* ", 
    " FROM (SELECT FV.*, COUNT(FVT.visit_type_id) AS count_visit
        FROM (
            SELECT FR.form_id, FR.form_type, FR.form_class, FR.form_title, FR.is_visit_related, 
            COUNT(field_id) AS count_field
            FROM (".Database::get_view_form_mapper().") FR 
            GROUP BY FR.form_id ) FV
        LEFT OUTER JOIN form_visit_type FVT ON FVT.form_id = FV.form_id
        GROUP BY FV.form_id
    ) F ", 
    "", []);


//--------------------------------FORMS TABLE
$thead = HTML::set_tr(
    HTML::set_td(Language::find('form_title'), '', true) .
    HTML::set_td(Language::find('class'), '', true) .
    HTML::set_td(Language::find('name'), '', true) .
    HTML::set_td(Language::find('type'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($forms as $frm) {
    URL::changeable_var_add('fid', $frm['form_id']);
    $color_f = $frm['count_field'].'' == '0' ? 'FFA79C' : '';
    $color_v = $frm['count_visit'].'' == '0' ? 'FFA79C' : '';
    $button_common = '';
    $button_common .= HTML::set_button(Icon::set_edit().Language::find('modify'), '', URL::create_url('form'));
    $button_common .= HTML::set_button(Icon::set_list().Language::find('fields').' ('.$frm['count_field'].')', '', URL::create_url('fields'), '', '', '', $color_f);
    $button_common .= HTML::set_button(Icon::set_list().Language::find('visits').' ('.$frm['count_visit'].')', '', URL::create_url('visit_forms'), '', '', '', $color_v);
    $trs .= HTML::set_tr(
        HTML::set_td($frm['form_type']) .
        HTML::set_td($frm['form_class']) .
        HTML::set_td($frm['form_title'].' ('.Language::find($frm['form_title'], [$frm['form_class']]).')') .
        HTML::set_td(Language::find($frm['is_visit_related'].'' == '1' ? 'visit' : 'patient')) .
        HTML::set_td($button_common) 
    );
    //Icon::set_checker($frm['is_visit_related'].'' == '1', 1).' '.
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
HTML::$title = Language::find('forms');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));