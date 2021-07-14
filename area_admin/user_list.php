<?php

//--------------------------------VARIABLES
$trs = '';
$cols = [];


//--------------------------------NEW USER
URL::changeable_vars_reset();
URL::changeable_var_add('uid', 0);
$html .= HTML::set_button(Icon::set_add() . Language::find('add_new'), '', URL::create_url('user'));
URL::changeable_vars_reset();
$html .= HTML::BR;
$html .= HTML::BR;


//--------------------------------PATIENTS SELECT
$cols[] = ["id" => 'center_code', "label" => Language::find('center'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'email', "label" => Language::find('email'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'name', "label" => Language::find('name'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'surname', "label" => Language::find('surname'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'role', "label" => Language::find('role'), "type" => Paging_table::TYPE_SELECT, 
    "orderable" => true, "values" => Role::get_select()];
$cols[] = ["id" => 'enabled', "label" => Language::find('enabled'), "type" => Paging_table::TYPE_SELECT, 
    "orderable" => true, "default_value" => 1, "values" => [[0, Language::find('disabled')], [1, Language::find('enabled')]]]; 
$oPaging = new Paging_table('user_table', $cols);
$oPaging->add_order([["center_code", "ASC"], ["surname", "ASC"]]);
$where = $oUser->is_superadmin() ? "" : "WHERE role NOT IN ('wheel')";
$users = $oPaging->read('id_user', "SELECT U.* ", " FROM ".User::get_table()." U ", $where, []);
$oUsers = [];
foreach($users as $usr){
    $oUsers[] = new User('', $usr);
}

//--------------------------------USERS TABLE
$thead = HTML::set_tr(
    HTML::set_td(Language::find('center'), '', true) .
    HTML::set_td(Language::find('email'), '', true) .
    HTML::set_td(Language::find('name'), '', true) .
    HTML::set_td(Language::find('surname'), '', true) .
    HTML::set_td(Language::find('role'), '', true) .
    HTML::set_td(Language::find('enabled'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($oUsers as $oUsr) {
    URL::changeable_var_add('uid', $oUsr->id);
    $button_common = '';
    $button_common .= HTML::set_button(Icon::set_edit().Language::find('modify'), '', URL::create_url('user'));
    $color = Role::get_color($oUsr->role);
    if ($color != '') { $color = 'background-color: '. $color; }
    $trs .= HTML::set_tr(
        HTML::set_td($oUsr->center) .
        HTML::set_td($oUsr->email) .
        HTML::set_td($oUsr->name) .
        HTML::set_td($oUsr->surname) .
        HTML::set_td(Language::find($oUsr->role), '', false, '', $color) .
        HTML::set_td(Icon::set_checker($oUsr->enabled), '', false, '', 'text-align: center; ') .
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
if ($oUser->is_superadmin()) {
    $html .= HTML::set_button(Icon::set_back() . Language::find('home'), '', URL::create_url('home'), '', 'float:left;');
} else {
    $html .= HTML::set_button(Icon::set_back() . Language::find('patients'), '', URL::create_url('patients'), '', 'float:left;');
}
HTML::$title = Language::find('users');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));
