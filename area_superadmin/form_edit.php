<?php
if (!$oUser->is_superadmin()) {
    URL::changeable_vars_reset();
    URL::redirect('error', 1);
}

//--------------------------------VARIABLES
$oEditFrm = new Form();
$oEditFrm->get_by_id(URL::get_onload_var('fid') == '' ? 0 : URL::get_onload_var('fid'), 0, false);
$is_view = false;
$form = '';
Language::add_area('form');
$langs = Language::get_all();


//--------------------------------FUNCTIONS
function create_class($name) {
    if (in_array($name, ['patient_criteria'])) { return; }
    $uClass = ucfirst($name);
    $file_name = Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . 'crf' . Config::PATH_SEP . $uClass . '.php';
    if (file_exists($file_name)) { URL::redirect('form', 7); }
    $file = fopen($file_name, "w") or URL::redirect('form', 8);
    $text = '<?php
class '.$uClass.' extends Abstract_CRF_page {
    private $form = "";
    function get_draw_custom_html($page_number) {
        
        return $this->form;
    }
}
';
    fwrite($file, $text);
    fclose($file);
}


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act != '') {
    if ($act == 'save') {
        $oEditFrm->type = Security::sanitize(INPUT_POST, "form_type");
        $oEditFrm->class = Security::sanitize(INPUT_POST, "form_class");
        $oEditFrm->title = Security::sanitize(INPUT_POST, "form_title");
        $oEditFrm->is_visit_related = Security::sanitize(INPUT_POST, "is_visit_related");
        $params = [$oEditFrm->type, $oEditFrm->class, $oEditFrm->title, $oEditFrm->is_visit_related];
        if ($oEditFrm->id == 0) {
            Database::edit("INSERT INTO form (form_type, form_class, form_title, is_visit_related) VALUES (?, ?, ?, ?)", $params);
        } else {
            $params[] = $oEditFrm->id;
            Database::edit("UPDATE form SET form_type = ?, form_class = ?, form_title = ?, is_visit_related = ? WHERE form_id = ?", $params);
        }
        Database::create_form($oEditFrm->class, $oEditFrm->is_visit_related.'' == '1');
        foreach($langs as $lang) {
            $iso = $lang['languageiso'];
            Language::save($oEditFrm->title, $iso, $oEditFrm->class, Security::sanitize(INPUT_POST, $iso));
        }
        create_class($oEditFrm->class);
    } else if ($act == 'delete') {
        if (count($oEditFrm->oFields) > 0) {
            URL::changeable_var_add('fid', $oEditFrm->id);
            URL::redirect('form', 1);
        }
        $found = Database::read("SELECT * FROM form_visit_type WHERE form_id = ? ", [$oEditFrm->id]);
        if (count($found) > 0) {
            URL::changeable_var_add('fid', $oEditFrm->id);
            URL::redirect('form', 1);
        }
        Database::edit("DELETE FROM form WHERE form_id = ? ", [$oEditFrm->id]);
    }
    URL::changeable_vars_reset();
    URL::redirect('forms');
}


//--------------------------------FORM
$form .= Form_input::createInputText('form_type', Language::find('name'), $oEditFrm->type, 4, false, "check_text('form_type','2','100');", 255, $is_view);
$form .= Form_input::createInputText('form_class', Language::find('class'), $oEditFrm->class, 4, true, "check_text('form_class','2','50');", 255, $is_view);
$title = Language::find('form_title');
if ($oEditFrm->id != 0) { $title .= ' ('.$oEditFrm->get_title().')'; }
$form .= Form_input::createInputText('form_title', $title, $oEditFrm->title, 8, true, "check_text('form_title','2','50');", 255, $is_view);

foreach($langs as $lang) {
    $iso = $lang['languageiso'];
    $form .= Form_input::createInputText($iso, $lang['translated'], Language::get($oEditFrm->title, $iso), 3, false, "check_text('".$iso."','2','100');", 255, $is_view);
}
$form .= '</div><div class="row">';

$form .= Form_input::createLabel('is_visit_related', Language::find('type'));
$is_visit_related = $oEditFrm->id == 0 ? NULL : ($oEditFrm->is_visit_related ? 1 : 0);
$form .= Form_input::createRadio('is_visit_related', Language::find('visit'), $is_visit_related, 1, 3, false, "check_radio('is_visit_related');", $is_view);
$form .= Form_input::createRadio('is_visit_related', Language::find('patient'), $is_visit_related, 0, 3, true, "check_radio('is_visit_related');", $is_view);

$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');
$html .= HTML::BR;


//--------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . Language::find('save'), "$('#act').val('save'); page_validation('form1');", '', '', 'float:right;');
    if ($oEditFrm->id != 0) {
        $text = str_replace('{0}', '<b>'.$oEditFrm->get_title().'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('frm_delete', Language::find('delete').' '.$oEditFrm->get_title(), $text, Language::find('delete'), 
            "$('#act').val('delete'); $('#form1').submit();", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#frm_delete').modal('show');", '', '', 'float:right;');
    }
}


//--------------------------------HTML
HTML::$title = Language::find('form_title').'<br>';
if ($oEditFrm->id == 0) {
    HTML::$title .= Language::find('add_new');
} else {
    HTML::$title .= $oEditFrm->get_title().' - '.count($oEditFrm->oFields).' '.Language::find('fields');
}
URL::changeable_vars_reset();
$html .= HTML::set_button(Icon::set_back() . Language::find('forms'), '', URL::create_url('forms'), '', 'float:left;');
$html .= HTML::BR;
HTML::print_html($html);