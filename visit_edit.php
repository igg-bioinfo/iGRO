<?php

if ($oPatient->id == 0) {
    URL::redirect('error', 1);
}


//--------------------------------VARIABLES
Language::add_area('visit');
$form = '';
$is_view = $oVisit->is_lock;
$is_new = $oVisit->id == 0;
$js_onload = '';
$oAuthor = new User();
$oAuthor->get_by_id($oVisit->author);
HTML::$js .= ' var NO_TYPE = "'.Language::find('no_type').'"; ';


//--------------------------------FUNCTIONS
function loading_html() {
    $html = "<div style='text-align:center;'>";
    $html .= Icon::set_loading();
    $html .= Language::find('loading')."...";
    $html .= "</div>";
    return $html;
}


//--------------------------------POST ACT
$act = Security::sanitize(INPUT_POST, 'act');
if ($act == 'save') {
    $date = Security::sanitize(INPUT_POST, 'date_visit');
    if ($date.'' == '') {
        URL::redirect('visit', 500);
    }
    $oVisit->date = Date::screen_to_default($date);
    $oVisit->visit_type_id = Security::sanitize(INPUT_POST, "select_vtype");
    if ($is_new) {
        $oVisit->id_paz = $oPatient->id;
        $entered = Database::read("SELECT * FROM visit WHERE id_paz = ? AND date_visit = ?", [$oVisit->id_paz, $oVisit->date]);
        if (count($entered)) {
            URL::changeable_vars_reset();
            URL::changeable_var_add('pid', $oVisit->id_paz);
            URL::redirect('visits');
        }
        $oVisit->create();
        /*
        if ($visit_type_id != '') {

            //SUBJECT
            $subject = "SUBJECT " . $oPatient->patient_id . " HAS A NEW VISIT FOR THE STUDY: " . $oEnrollProj->code;

            //MESSAGE
            $message = "Dear Staff," . HTML::BR . HTML::BR;
            $message .= "Subject " . $oPatient->patient_id . " has a new visit for the study:" . HTML::BR . HTML::BR;
            $message .= $oEnrollProj->name . HTML::BR;
            URL::changeable_vars_reset();
            $message .= "Go to " . URL::create_url('', 'admin') . " and check it" . HTML::BR . HTML::BR;
            URL::changeable_vars_from_onload_vars();
            $message .= "Admin";

            //EMAILER
            $oMailer = new Mailer();
            $oMailer->set_subject($subject);
            $oMailer->set_message($message);
            //$recipients = [Mailer::get_admin(false)];
            $oMailer->send($recipients);
        }
        */
    }else {
        $oVisit->update();
    }
    URL::changeable_vars_reset_except(['pid']);
    //URL::changeable_var_add('pid', $oPatient->id);
    URL::redirect('visits');
} else if ($act == 'delete') {
    $oVisit->delete();
    URL::changeable_vars_reset_except(['pid']);
    URL::redirect('visits');

}


//--------------------------------JS VALUE TRANSLATED
HTML::$js .= ' var TRANS_TYPE = "'.Language::find('type').'"; ';
HTML::$js .= ' var TRANS_SELECT = "'.Language::find('select_one_option', ['validation']).'"; ';


//--------------------------------JS AJAX
$date_disabled = $oVisit->is_lock || $oVisit->has_forms_started();
HTML::add_link('crf/visit_types', 'js');
URL::changeable_var_add("fn", Globals::AJAX_VISIT_TYPE);
URL::changeable_var_add("pid", $oPatient->id);
URL::changeable_var_add("vid", $oVisit->id);
$ajax_url = URL::create_url("ajax");
URL::changeable_vars_reset();
JS::set_login_redirect();
$js_params = 'date: visit_date ';
HTML::$js .= JS::set_func('get_visit_types', '$("#vtype_html").html("' . loading_html() . '"); ' . 
    JS::set_ajax($ajax_url, 'json', 'post', $js_params, 'visit_types_done(data);', '', 'visit_types_fail(error);'), 'visit_date');
if ($date_disabled) {
    HTML::$js_onload .= ' get_visit_types("' . Date::default_to_screen($oVisit->date) . '"); ';
    $form .= Form_input::createHidden('date_visit', Date::default_to_screen($oVisit->date));
}


//--------------------------------FORM
$form .= Form_input::createDatePicker('date_visit', Language::find('date'), $oVisit->date, 4, true, 
    "$('#vtype_html').html(''); if(check_text('date_visit', 11)) { if (check_date('date_visit')) get_visit_types($('#date_visit').val()); } ", $date_disabled);
$form .= '<div id="vtype_html" class="form-group col-xs-12 col-sm-4">';
$form .= '</div>';
$form .= Form_input::br(true);
$form .= Form_input::createHidden('act');
$html .= HTML::set_form($form, 'form1', '');


//---------------------------------BUTTONS
if (!$is_view) {
    $html .= HTML::set_button(Icon::set_save() . ($is_new ? Language::find('new_visit') : Language::find('save')), "$('#act').val('save'); page_validation('form1');", '', 'btn_save', 'float:right;');
    if (!$date_disabled) {
        $text = str_replace('{0}', '<b>'.$oVisit->type_text.'</b>', Language::find('delete_confirmation'));
        $html .= Form_input::createPopup('vis_delete', Language::find('delete').' '.$oVisit->type_text, $text, Language::find('delete'), 
            "$('#act').val('delete'); page_validation('form1');", Language::find('no'));
        $html .= HTML::set_button(Icon::set_remove() . Language::find('delete'), "$('#vis_delete').modal('show');", '', '', 'float:right;');
    }
}
URL::changeable_vars_reset();
URL::changeable_var_add('pid', $oPatient->id);
$html .= HTML::set_button(Icon::set_back() . Language::find('visits'), '', URL::create_url('visits'), '', 'float:left;');


//-----------------------------------HTML
HTML::set_audit_trail($oAuthor, $oVisit->ludati);
HTML::$title = ($is_new ? Language::find('new_visit') : ($is_view ? '' : Language::find('modify').' ').Language::find('visit'));
HTML::$title .= '<br>' . $oPatient->patient_id;
HTML::print_html($html);