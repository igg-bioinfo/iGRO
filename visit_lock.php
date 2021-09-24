<?php

//--------------------------------VARIABLES
const SAVE_LOCK = 1;
const SAVE_UNLOCK = 2;
const SAVE_CHECK = 3;
const SAVE_UNCHECK = 4;

$html = '';
$page_type = URL::get_onload_var('pg');
$is_error = false;
$is_admin = $oArea->id == Area::$ID_ADMIN;
$is_completed = true;
Language::add_area('visit');
if ($page_type . '' == '' || $oVisit->id == 0) {
    URL::redirect('error', 1);
}
if (URL::get_onload_var(Visit::CONFIRM_TYPE_VLOCK) === ($oVisit->is_lock ? 1 : 0)) {
    URL::redirect('error', 1);
}
if (URL::get_onload_var(Visit::CONFIRM_TYPECHECK) === ($oVisit->is_check ? 1 : 0)) {
    URL::redirect('error', 1);
}


//--------------------------------FUNCTION
function set_error_warning_icon($is_error, $size = 2) {
    return Icon::set($is_error ? 'close' : 'exclamation-triangle', $size, 'testo' . ($is_error ? 'rosso' : 'arancio'));
}

function set_table_title($text, $colspan, $is_subtitle = false) {
    return HTML::set_tr(HTML::set_td($text, $colspan, true, '', 'background-color: #' . ($is_subtitle ? 'EAEAEA' : 'DAF3FE')));
}

function set_row_4cell($label1, $text1, $label2, $text2) {
    return HTML::set_tr(HTML::set_td('<b>' . $label1 . '</b>') . HTML::set_td($text1) . HTML::set_td('<b>' . $label2 . '</b>') . HTML::set_td($text2));
}

function send_confirmation() {
    global $oUser, $oVisit, $oPatient;
    $subject = "Data confirmed for subject " . $oPatient->patient_id;
    $message = "Dear Staff," . HTML::BR . HTML::BR;
    $message .= "Subject " . $oPatient->patient_id . " data has just been confirmed for visit "
        .Date::default_to_screen($oVisit->date) . " (".$oVisit->type_text.")." . HTML::BR . HTML::BR;
    $message .= "Data confirmed by: " . $oUser->name . " " . $oUser->surname . HTML::BR;
    $message .= "Administrator (AUTOMATED EMAIL)";
    $oMailer = new Mailer();
    $oMailer->set_subject($subject);
    $oMailer->set_message($message);
    $oMailer->send([Mailer::get_admin()]);
}

function send_output_checked($oOutput, $oRecipients) {
    $subject = Security::sanitize(INPUT_POST, 'output_subj');
    $message = Security::sanitize(INPUT_POST, 'output_msg');
    $message = str_replace(Mailer::VAR_RESPONSE, $oOutput->result, $message);
    $oMailer = new Mailer();
    $oMailer->set_subject($subject);
    $oMailer->set_message($message);
    $oMailer->send($oRecipients, Config::SITEVERSION == 'TEST'); //add a true for debug
}

function get_oOutput($oVis) {
    $oOutput = NULL;
    $class_output = 'Output_' . $oVis->type;
    if (class_exists($class_output)) {
        $oOutput = new $class_output($oVis);
    }
    return $oOutput;
}

function get_output_oRecipients($oPatient) {
    $oOutputRecipients = User::get_investigators($oPatient->oCenter->id, true);
    $oOutputRecipients[] = Mailer::get_admin();
    return $oOutputRecipients;
}

function get_output_recipients_text($oOutputRecs) {
    $output_recipients = '';
    foreach ($oOutputRecs as $oOutputRec) {
        $output_recipients .= $oOutputRec->email . ', ';
    }
    return $output_recipients;
}

//--------------------------------------CONFIRM / UNLOCK / CHECK
$post_save = Security::sanitize(INPUT_POST, 'save_confirm');
if ($post_save == SAVE_LOCK) {
    if (Security::sanitize(INPUT_POST, 'lock_reason') != '') {
        $oVisit->lock_reason = Security::sanitize(INPUT_POST, 'lock_reason');
    }
    Query::save_all($oVisit);
    $oVisit->update_lock();
    $has_output = false;
    if ($oVisit->has_output) {
        $oOutput = get_oOutput($oVisit);
        if (isset($oOutput)) {
            $oOutput->calculate();
            $oOutput->save();
            $has_output = $oOutput->has_output;
        }
    }
    send_confirmation();
    URL::changeable_vars_reset();
    URL::changeable_var_add('pid', $oPatient->id);
    if ($has_output) {
        URL::changeable_var_add('vid', $oVisit->id);
        URL::redirect('output');
    } else {
        URL::redirect('visits');
    }
} else if ($post_save == SAVE_UNLOCK) {
    if (Security::sanitize(INPUT_POST, 'unlock_reason') != '') {
        $oVisit->unlock_reason = Security::sanitize(INPUT_POST, 'unlock_reason');
    }
    $oVisit->update_unlock();
    URL::redirect('visits');
} else if ($post_save == SAVE_CHECK || $post_save == SAVE_UNCHECK) {
    if (Security::sanitize(INPUT_POST, 'check_note') != '') {
        $oVisit->check_note = Security::sanitize(INPUT_POST, 'check_note');
    }
    if ($post_save == SAVE_CHECK && $oVisit->has_output) {
        $oOutput = get_oOutput($oVisit);
        if (isset($oOutput)) {
            $to_send = Security::sanitize(INPUT_POST, 'output_send') == '1';
            $oOutput->result = Security::sanitize(INPUT_POST, 'output');
            $oOutputRecs = get_output_oRecipients($oPatient);
            if ($to_send) {
                $oOutput->recipients = get_output_recipients_text($oOutputRecs);
            }
            $oOutput->save(true);
            if ($to_send) {
                send_output_checked($oOutput, $oOutputRecs);
            }
        }
    }
    $oVisit->update_check($post_save == SAVE_CHECK);
    URL::redirect('visits');
}


//--------------------------------PATIENT DETAILS
$trs = '';
$trs .= set_table_title(strtoupper(Language::find('details')), 4);
$trs .= set_row_4cell(Language::find('patient_code'), $oPatient->patient_id, Language::find('visit'), $oVisit->type_text.' - '.Date::default_to_screen($oVisit->date));
if ($is_admin) {
    $trs .= set_row_4cell('ID '.Language::find('patient'), $oPatient->id, 'ID '.Language::find('visit'), $oVisit->id);
} else {
    $oPatient->oCenter = $oUser->oCenter;
    $oPatient->decrypt();
    $trs .= set_row_4cell(Language::find('name'), $oPatient->first_name, Language::find('surname'), $oPatient->last_name);
}
$html .= HTML::set_table($trs);
$html .= HTML::BR;


//--------------------------------------QUERIES
$oQueries = Query::get_all_by_visit($oVisit);
if (count($oQueries)) {
    $trs = '';
    $tds = '';
    $trs .= set_table_title(strtoupper(Language::find('auto_check')), 3);
    foreach ($oQueries as $oQuery) {
        if ($oQuery->is_blocking) {
            $is_error = true;
        }
        $tds = '';
        $tds .= HTML::set_td(set_error_warning_icon($oQuery->is_blocking), '', false, '', 'width: 5px;');
        $tds .= HTML::set_td($oQuery->description);
        $tds .= HTML::set_td($oQuery->action);
        $trs .= HTML::set_tr($tds);
    }
    $html .= HTML::set_table($trs);
    $html .= HTML::BR;
}


//--------------------------------CONFIRM / UNLOCK / ADMIN CHECK BUTTON
$html .= HTML::BR;
if (!$is_completed) {
    //HTML::$error_text = "BEFORE CONFIRMING THE VISIT YOU HAVE TO COMPLETE ALL THE STUDIES INVOLVED";
    $html .= HTML::set_td(HTML::set_button(Icon::set_back() . 'visits', '', URL::create_url('visits'), '', ''), '', true);
    $html .= HTML::BR;
} else if ($is_error && !$oVisit->is_lock) {
    //HTML::$error_text = "BEFORE CONFIRMING THE VISIT YOU HAVE TO SOLVE THE QUERY WITH THE RED X ABOVE";
    $html .= HTML::set_td(HTML::set_button(Icon::set_back() . 'visits', '', URL::create_url('visits'), '', ''), '', true);
    $html .= HTML::BR;
} else if ($is_admin || !$oVisit->is_lock || Config::INVESTIGATOR_CAN_UNLOCK_VISIT) {
    $html .= Form_input::createHidden('save_confirm', "");

    $trs = '';
    $tds = '';
    $btn = '';
    $btn .= HTML::set_button(Icon::set_back().strtoupper(Language::find('no').', '.Language::find('back_to').' '.Language::find('visits')), '', URL::create_url('visits'), '', 'float: left');
    $btn .= HTML::set_button(Icon::set_back().strtoupper(Language::find('no').', '.Language::find('back_to').' '.Language::find('visit')), '', URL::create_url('visit_index'), '', 'float: left');
    $tds .= HTML::set_td($btn, '', true);

    //-----------------------------VISIT LOCK UNLOCK
    if ($page_type == Visit::CONFIRM_TYPE_VLOCK) {
        $trs .= set_table_title(Icon::set_warning() . strtoupper(Language::find(($oVisit->is_lock ? 'un' : '').'lock_question')) . Icon::set_warning(), 2);
        if ($oVisit->is_lock) {
            $trs .= set_table_title(Form_input::createTextareaBasic('unlock_reason', Language::find('note'), $oVisit->unlock_reason, 3, 'check_text(\'unlock_reason\',10);'), 2, true);
        } else {
            if (!Config::INVESTIGATOR_CAN_UNLOCK_VISIT) {
                $trs .= set_table_title(Language::find('alert_email_unlock'), 2, true);
            }
            if ($is_admin) {
                $trs .= set_table_title(Form_input::createTextareaBasic('lock_reason', 'Specify reasons for locking from admin', $oVisit->lock_reason, 3, 'check_text(\'lock_reason\',10);'), 2, true);
            }
        }
        $save_confirm_js = " $('#save_confirm').val('" . ($oVisit->is_lock ? SAVE_UNLOCK : SAVE_LOCK) . "'); ";
        $tds .= HTML::set_td(HTML::set_button(Icon::set_save() . strtoupper(Language::find('yes').', '.Language::find($oVisit->is_lock ? 'unlock' : 'confirm') . ' '.Language::find('visit')), $save_confirm_js . " page_validation('form1');", '', '', 'float: right;'), '', true);
    }

    //-----------------------------ADMIN CHECK
    if ($page_type == Visit::CONFIRM_TYPECHECK) {
        $oOutput = get_oOutput($oVisit);
        Language::add_area('email');
        if ($is_admin && $oVisit->is_lock) {
            $check_text = Language::find('quality_check');
            $trs .= set_table_title(strtoupper($check_text), 2);
            $trs .= set_table_title(Form_input::createTextareaBasic('check_note', Language::find('note'), $oVisit->check_note, 3, ''), 2, true);
            $tds = '';
            $save_confirm_js = " $('#save_confirm').val('" . ($oVisit->is_check ? SAVE_UNCHECK : SAVE_CHECK) . "'); ";

            //-----------REMOVE CHECK
            if ($oVisit->is_check) {
                if ($oVisit->has_output) {
                    if (isset($oOutput) && $oOutput->need_check && $oOutput->result . '' != '') {
                        $title = '<hr><b>'.Language::find('output').'</b>' . HTML::BR;
                        $trs .= HTML::set_tr(HTML::set_td($title . $oOutput->result));
                        if ($oOutput->recipients != '') {
                            $trs .= HTML::set_tr(HTML::set_td('<b>'.Language::find('recipients').'</b><br>' . $oOutput->recipients));
                        }
                    }
                }
                $btn = HTML::set_button(Icon::set_save() .strtoupper(Language::find('delete').' '.$check_text), $save_confirm_js . " page_validation('form1');", '', '', 'float: right;');
                if ($is_admin) {
                    $btn .= HTML::set_button(Icon::set_back().strtoupper(Language::find('visits')), '', URL::create_url('visits'), '', 'float: left');
                    $btn .= HTML::set_button(Icon::set_back().strtoupper(Language::find('visit')), '', URL::create_url('visit_index'), '', 'float: left');
                }
                $tds .= HTML::set_td($btn, '', true);
            }

            //-----------MARK CHECK
            else {
                if ($oVisit->has_output && isset($oOutput) && $oOutput->need_check) {
                    $subj = strtoupper(Language::find('patient')." " . $oPatient->patient_id 
                        . " - " . $oVisit->type_text . " " .Date::default_to_screen($oVisit->date). " - ".Language::find('output'));
                    $output_subj = Form_input::createInputText('output_subj', Language::find('object'), $subj, 12, true, '', 300);
                    $trs .= HTML::set_tr(HTML::set_td($output_subj));
    
                    $msg = Mailer::get_msg_output();
                    $msg = str_replace(Mailer::VAR_VISIT_TYPE, $oVisit->type_text.' - '.$oVisit->type_code, $msg);
                    $msg = str_replace(Mailer::VAR_DATE, Date::default_to_screen($oVisit->date), $msg);
                    $msg = str_replace(Mailer::VAR_PTCODE, $oPatient->patient_id, $msg);
                    $output_msg = Form_input::createTextEditor('output_msg', Language::find('body'), $msg, 12, 200, true, '');
                    $trs .= HTML::set_tr(HTML::set_td($output_msg));
    
                    $output_response = Form_input::createTextEditor('output', Language::find('output'), $oOutput->result, 12, 400, true, '');
                    $trs .= HTML::set_tr(HTML::set_td($output_response));
                    $output_extras = $oOutput->draw_extra_field_inputs();
                    if ($output_extras != '') {
                        $trs .= HTML::set_tr(HTML::set_td($output_extras));
                    }
                    $oOutputRecs = get_output_oRecipients($oPatient);
                    $output_recipients = get_output_recipients_text($oOutputRecs);
                    if ($output_recipients != '') {
                        $trs .= HTML::set_tr(HTML::set_td('<b>'.Language::find('recipients').'</b><br>' . $output_recipients));
                    }
                    $output_send_id = 'output_send';
                    $output_send = '<b>'.Language::find('send').' '.strtolower(Language::find('email')).'</b>';
                    $output_send .= Form_input::createRadio($output_send_id, Language::find('yes'), '', 1, 2, false, "check_radio('" . $output_send_id . "');");
                    $output_send .= Form_input::createRadio($output_send_id, Language::find('no'), '', 0, 2, false, "check_radio('" . $output_send_id . "');");
                    $trs .= HTML::set_tr(HTML::set_td($output_send));

                }
                $btn = HTML::set_button(Icon::set_save() . strtoupper(Language::find('confirm').' '.$check_text), $save_confirm_js . " page_validation('form1');", '', '', 'float: right;');
                if ($is_admin) {
                    $btn .= HTML::set_button(Icon::set_back().strtoupper(Language::find('visits')), '', URL::create_url('visits'), '', 'float: left');
                    $btn .= HTML::set_button(Icon::set_back().strtoupper(Language::find('visit')), '', URL::create_url('visit_index'), '', 'float: left');
                }
                $tds .= HTML::set_td($btn, '', true);
                
            }
        }
    }
    $trs .= HTML::set_tr($tds);
    $html .= HTML::set_table($trs);
    $html = HTML::set_form($html, 'form1', '');
}

URL::changeable_vars_reset();
URL::changeable_var_add('pid', $oPatient->id);

//--------------------------------------HTML
HTML::$title = Language::find('confirm_group') . '<br>';
HTML::$title .= $oVisit->type_text.' ' . Date::default_to_screen($oVisit->date);
HTML::print_html($html);