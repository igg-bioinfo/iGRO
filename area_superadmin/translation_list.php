<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$trs = '';
$cols = [];
$is_view = false;
$areas = LAnguage::get_areas();
$area = URL::get_onload_var('aid') == '' ? 'general' : '';
Language::add_area($area);
$form = '';
$langs = Language::get_all();


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');


//--------------------------------FORM
$form .= Form_input::createInputText('area_text', Language::find('area'), '', 4, false, "check_text('area_text','','','',true);", 255, $is_view);
foreach($langs as $lang) {
    $iso = $lang['languageiso'];
    $form .= Form_input::createInputText($iso, $lang['translated'], '', 3, false, "check_text('".$iso."','','','',false);", 255, $is_view);
}
$form .= '</div><div class="row">';
$form .= '</div><div class="row"><div class="col-lg-12 col-md-12">';
$form .= HTML::set_button("", " $('#act').val('update'); page_validation('form1'); ", '', "update_btn", 'display:none;');
$form .= '</div></div><div class="row">';
$form .= Form_input::br(true);


//--------------------------------TABLE
$thead = HTML::set_tr(
    HTML::set_td('', '', true) .
    HTML::set_td(Language::find('type'), '', true) .
    HTML::set_td('', '', true), true
);
$js_sel = '';
foreach (Language::$area_translations[$area] as $row) {
    $trs .= HTML::set_tr(
        HTML::set_td('<div style="white-space: nowrap;">'.$row['label_text'].'</div>') .
        HTML::set_td('<div style="white-space: nowrap;">'.$row['translation'].'</div>') .
        HTML::set_td('<div style="white-space: nowrap;">'.$row['english'].'</div>') 
    );
}
$js = 'columnDefs: [
    {width: "5px", targets: [0]}, 
    {width: "5px", targets: [1]}, 
    {orderable: false, targets: [2]},
    {className: "responsive-table-dynamic-column", "targets": [0,1]}
    ], '.JS::set_responsive_lang().' ';
$form .= HTML::set_bootstrap_cell(HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'tr_forms', $js), 12);
$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', 'style="width: 100%"');
$html .= HTML::BR;


//--------------------------------------HTML
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('home'), '', URL::create_url('home'), '', 'float:left;');
HTML::$title = Language::find('translations');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));