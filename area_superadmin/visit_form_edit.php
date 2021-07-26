<?php

//--------------------------------VARIABLES
$form_id = URL::get_onload_var('fid') == '' ? 0 : URL::get_onload_var('fid');
$vt_id = URL::get_onload_var('vtid') == '' ? 0 : URL::get_onload_var('vtid');
$is_form = $form_id != 0;
$is_vt = $vt_id != 0;
if ((!$is_form && !$is_vt) || ($is_form && $is_vt) || !$oUser->is_superadmin()) { 
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}
$title = '';
$subtitle = Language::find($is_form ? 'visits' : 'forms');
$rows = [];
$rows_sel = [];
$trs = '';
$cols = [];
$is_view = false;
$form = '';
Language::add_area('visit');
Language::add_area('form');
$langs = Language::get_all();


//--------------------------------FUNCTIONS
function is_selected($id, $title, &$js_sel) {
    global $rows_sel, $is_form, $langs;
    $js_sel = '';
    foreach($rows_sel as $sel) {
        if ($id == $sel['id']) {
            $js_sel = " $('#group_name').val('".$sel['group_name']."'); ";
            $js_sel .= " $('#order_id').val('".$sel['order_id']."'); ";
            $js_sel .= " $('#dependencies').val('".$sel['dependencies']."'); ";
            $js_sel .= " $('#is_required_".$sel['order_id']."').prop('checked', true); "; 
            $js_sel .= " $('#update_btn').html('".Language::find('modify')." ".str_replace("&#039;", " ", $title)."'); "; 
            $js_sel .= " $('#update_btn').show(); "; 
            $js_sel .= " $('#".($is_form ? "visit_type_id" : "form_id")."').val('".$sel['id']."'); ";
            $js_sel .= " check_text('group_name','','','',false); check_integer('order_id','','', true); check_radio('is_required');";
            
            foreach($langs as $lang) {
                $iso = $lang['languageiso'];
                $js_sel .= " $('#".$iso."').val('".Language::get($sel['group_name'], $iso)."'); "; 
                $js_sel .= " check_text('".$iso."','','','',false); "; 
            }
            return true;
        }
    }
    return false;
}


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    $visit_type_id = Security::sanitize(INPUT_POST, 'visit_type_id');
    $form_id = Security::sanitize(INPUT_POST, 'form_id');
    if ($visit_type_id.'' == '' || $form_id.'' == '') {
        URL::changeable_vars_reset();
        URL::redirect('error', 500);
    }
    if ($act == 'delete') {
        Database::edit("DELETE FROM form_visit_type WHERE visit_type_id = ? AND form_id = ?", 
            [$visit_type_id, $form_id]);
    } else {
        $is_required = Security::sanitize(INPUT_POST, 'is_required');
        $group = Security::sanitize(INPUT_POST, 'group_name');
        $order_id = Security::sanitize(INPUT_POST, 'order_id');
        $dependencies = Security::sanitize(INPUT_POST, 'dependencies');
        if ($act == 'save') { 
            Database::edit("INSERT INTO form_visit_type (visit_type_id, form_id, is_required, order_id, group_name, dependencies) 
                VALUES (?, ?, ?, ?, ?, ?);", 
                [$visit_type_id, $form_id, $is_required, $order_id, $group, $dependencies]);
        } else if ($act == 'update') {
            Database::edit("Update form_visit_type SET is_required = ?, order_id = ?, group_name = ?, dependencies = ? 
                WHERE visit_type_id = ? AND form_id = ?;", 
                [$is_required, $order_id, $group, $dependencies, $visit_type_id, $form_id]);
        } 
        foreach($langs as $lang) {
            $iso = $lang['languageiso'];
            Language::save($group, $iso, 'visit', Security::sanitize(INPUT_POST, $iso));
        }
    }
    URL::changeable_vars_from_onload_vars();
    URL::redirect('visit_forms');
}


//--------------------------------OBJ
$oEdit = NULL;
$sql = "SELECT {0} as id, is_required, order_id, group_name, dependencies FROM form_visit_type FVT WHERE FVT.{1} = ?";
if ($is_form) {
    $oEdit = new Form();
    $oEdit->get_by_id($form_id, 0, false);
    $title = $oEdit->get_title();
    $rows = Database::read("SELECT visit_type_id as id, visit_type, visit_type_code, is_extra FROM visit_type ORDER BY visit_type", []);
    for ($r = 0; $r < count($rows); $r++) {
        $rows[$r]['title'] = $rows[$r]['visit_type_code'].' - '.Language::find($rows[$r]['visit_type']);
        $rows[$r]['details'] = Language::find(($rows[$r]['is_extra'].'' == '1' ? 'extra_' : '').'visit');
    }
    $sql = str_replace("{0}", "visit_type_id", $sql);
    $rows_sel = Database::read(str_replace("{1}", "form_id", $sql), [$oEdit->id]);
} else if ($is_vt) {
    Visit_type::$visit_list_mode = false;
    $oEdit = new Visit_type();
    $oEdit->get_by_id($vt_id);
    $title = $oEdit->get_name();
    $rows = Database::read("SELECT form_id as id, form_title, form_class, is_visit_related FROM form ORDER BY form_type", []);
    for ($r = 0; $r < count($rows); $r++) {
        $rows[$r]['title'] = Language::find($rows[$r]['form_title'], [$rows[$r]['form_class']]);
        $rows[$r]['details'] = Language::find($rows[$r]['is_visit_related'] ? 'visit' : 'patient');
    }
    $sql = str_replace("{0}", "form_id", $sql);
    $rows_sel = Database::read(str_replace("{1}", "visit_type_id", $sql), [$oEdit->id]);
}


//--------------------------------FORM
$form .= Form_input::createInputText('group_name', Language::find('group'), '', 4, false, "check_text('group_name','','','',false);", 255, $is_view);
$form .= Form_input::createInputText('order_id', Language::find('order'), '', 2, true, "check_integer('order_id','','', true);", 255, $is_view);
foreach($langs as $lang) {
    $iso = $lang['languageiso'];
    $form .= Form_input::createInputText($iso, $lang['translated'], '', 3, false, "check_text('".$iso."','','','',false);", 255, $is_view);
}
$form .= '</div><div class="row">';
$form .= Form_input::createLabel('is_required', Language::find('required'));
$form .= Form_input::createRadio('is_required', Language::find('yes'), '', 1, 3, false, "check_radio('is_required');", $is_view);
$form .= Form_input::createRadio('is_required', Language::find('no'), '', 0, 3, true, "check_radio('is_required');", $is_view);
$form .= Form_input::createInputText('dependencies', Language::find('forms'), '', 6, true, "check_text('dependencies','','', '', false);", 255, $is_view);
$form .= '</div><div class="row"><div class="col-lg-12 col-md-12">';
$form .= HTML::set_button("", " $('#act').val('update'); page_validation('form1'); ", '', "update_btn", 'display:none;');
$form .= '</div></div><div class="row">';
$form .= Form_input::br(true);


//--------------------------------TABLE
$thead = HTML::set_tr(
    HTML::set_td($subtitle, '', true) .
    HTML::set_td(Language::find('type'), '', true) .
    HTML::set_td('', '', true), true
);
$js_sel = '';
foreach ($rows as $row) {
    $is_sel = is_selected($row['id'], $row['title'], $js_sel);
    $js = "$('#".($is_form ? "visit_type_id" : "form_id")."').val('".$row['id']."'); ";
    if ($is_sel) {
        $js .= " $('#act').val('delete'); $('#form1').submit(); ";
        $title_temp = HTML::set_button($row['title'], $js_sel);
    } else {
        $js .= " $('#act').val('save'); page_validation('form1'); ";
        $title_temp = $row['title'];
    }
    $color = $is_sel ? 'background-color: #CCFF66;' : '';
    $trs .= HTML::set_tr(
        HTML::set_td('<div style="white-space: nowrap;">'.$title_temp.'</div>', '', false, '', $color) .
        HTML::set_td('<div style="white-space: nowrap;">'.$row['details'].'</div>', '', false, '', $color) .
        HTML::set_td(HTML::set_button($is_sel ? Icon::set_remove().Language::find('delete') : Icon::set_add().Language::find('add'), $js), '', false, '', $color) 
    );
}
$js = 'columnDefs: [
    {width: "5px", targets: [0]}, 
    {width: "5px", targets: [1]}, 
    {orderable: false, targets: [2]},
    {className: "responsive-table-dynamic-column", "targets": [0,1]}
    ], '.JS::set_responsive_lang().' ';
$form .= HTML::set_bootstrap_cell(HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'vt_forms', $js), 12);
$form .= Form_input::createHidden('form_id', $is_form ? $oEdit->id : '');
$form .= Form_input::createHidden('visit_type_id', $is_vt ? $oEdit->id : '');
$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', 'style="width: 100%"');
$html .= HTML::BR;


//--------------------------------HTML
HTML::$title = $title.'<br>'.$subtitle;
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('visits'), '', URL::create_url('visit_types'), '', 'float:left;');
$html .= HTML::set_button(Icon::set_back() . Language::find('forms'), '', URL::create_url('forms'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);