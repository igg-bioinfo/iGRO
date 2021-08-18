<?php

function set_group($group, &$old_group) {
    if ($old_group != strtolower($group)) {
        $old_group = strtolower($group);
        return HTML::set_tr(HTML::set_td(strtoupper(Language::find($group)), 3, false, 'testorosso', 
            'font-size: 18px; font-weight: bold; border-top: 1px solid #555'));
    }
    return '';
}


function set_icon_and_title() {
    global $oForm, $is_form_completed, $form_access, $is_form_inserted, $form_text_block;
    $icon = '';
    if ($is_form_completed) {
        if ($form_text_block == '') {
            $icon = Icon::set('check', 2, 'testoverde');
        } else {
            $icon = Icon::set('exclamation-triangle', 2, 'testorosso');
        }
    } else if ($is_form_inserted) {
        if ($form_text_block == '') {
            $icon = Icon::set('close', 2, 'testorosso');
        } else {
            $icon = Icon::set('exclamation-triangle', 2, 'testorosso');
        }
    } else if ($form_access == FORM_REQUIRED) {
        $icon = Icon::set('close', 2, 'testorosso');
    } else if ($form_access == FORM_DISABLED) {
        $icon = Icon::set('check', 2, 'testoverde');
    } else if ($form_access == FORM_OPTIONAL) {
        $icon = Icon::set('plus', 2, 'testoarancio');
    }
    $ludati = isset($oForm->ludati) ? ' <span style="font-size: 11px; font-weight: bold">' . Date::default_to_screen($oForm->ludati) . '</span>' : '';
    return HTML::set_td($icon, '', false, '', 'width: 2px;') . HTML::set_td($oForm->get_title() . $ludati);
}


function set_form_buttons($oParents) {
    global $oForm, $is_visit_locked, $is_form_completed, $is_form_inserted, $form_text_block, $is_admin, $is_investigator;
    $buttons = '';
    $dependencies = false;

    URL::changeable_var_add('fid', $oForm->id);
    if (!$is_form_completed && $oForm->page_current != 0 ) {
        URL::changeable_var_add('page', $oForm->page_current + 1);
    }
    if (!$is_visit_locked) {//&& check_patient_enabled($error_patient)
        $dependencies = check_dependencies($oParents);
        if ($dependencies) {
            $buttons .= $dependencies;
        } else if ($form_text_block != '') {
            $buttons .= $form_text_block;
        } else {
            URL::changeable_var_add('act', 'edit');
            $buttons .= HTML::set_button($is_form_inserted ? Icon::set_edit() . Language::find($is_form_completed ? 'modify' : 'complete') : Icon::set_add() . Language::find('add_new'), '', URL::create_url(Globals::FORM_URL), '', 'text-align: left;');
        }
    }
    if (!$dependencies) {
        URL::changeable_var_add('act', 'view');
        URL::changeable_var_remove('page');
        if ($is_form_inserted) {

            //VIEW BUTTON
            $buttons .= HTML::set_button(Icon::set_view() . Language::find('view'), '', URL::create_url('form'));

            //DELETE BUTTON
            if (!$is_visit_locked && ($is_admin || ($is_investigator && Config::INVESTIGATOR_CAN_DELETE_VISIT))) {
                $buttons .= HTML::set_button(Icon::set_remove() . Language::find('delete'), " delete_data_form('".$oForm->type."', ".($oForm->is_visit_related ? '1' : '0').");");
            }
        }
    }
    return HTML::set_td($buttons, '', false, '', 'min-width: 250px;');
}


//-----------------------------------QUERIES
function set_error_warning_icon($is_error, $size = 2) {
    return Icon::set($is_error ? 'close' : 'exclamation-triangle', $size, 'testo' . ($is_error ? 'rosso' : 'arancio'));
}

function set_query_block() {
    global $oVisit, $is_visit_locked;
    $trs = '';
    if ($is_visit_locked) {
        $oQueries = Query::get_all($oVisit);
        foreach ($oQueries as $oQuery) {
            $tds = '';
            $tds .= HTML::set_td(set_error_warning_icon($oQuery->is_blocking));
            $tds .= HTML::set_td($oQuery->description);
            $trs .= HTML::set_tr($tds);
        }
        if ($trs != '') {
            $trs = set_group('auto_check', $group) . $trs;
        }
    }
    return $trs;
}


//-----------------------------------CONFIRM
function set_confirm_button($type, $is_to_confirm) {
    global $oVisit, $is_admin, $is_investigator, $can_be_locked, $can_be_unlocked;
    $html = '';

    URL::changeable_vars_reset();
    URL::changeable_var_add('pid', $oVisit->id_paz);
    URL::changeable_var_add('vid', $oVisit->id);
    URL::changeable_var_add($type, $is_to_confirm ? 0 : 1);
    URL::changeable_var_add('pg', $type);
    if ($type == Visit::CONFIRM_TYPE_VLOCK) {
        $btn_title = strtoupper(Language::find($is_to_confirm ? 'confirm' : 'unlock') . ' '. Language::find('visit'));
        if ($is_to_confirm && !$can_be_locked) {
            $html .= Language::find('cant_lock_prev');
        } else if ($is_investigator && !$is_to_confirm) {
            if (Config::INVESTIGATOR_CAN_UNLOCK_VISIT) {
                $html .= HTML::set_button($btn_title, '', URL::create_url('visit_lock'));
            } else {
                $form = '';
                $form .= Form_input::createHidden('unlock_mail_send');
                $unlock_question = Language::find('unlock_question');
                $form .= Form_input::createPopup('unlock_mail', $btn_title, $unlock_question, Language::find('send').' '.Language::find('email'), "$('#unlock_mail_send').val('SEND'); $('#form_unlock').submit();");
                $form .= HTML::set_button($btn_title, '$(\'#unlock_mail\').modal(\'show\');');
                $html .= HTML::set_form($form, 'form_unlock');
            }
        } else if (!$is_to_confirm && !$can_be_unlocked) {
            $html .= Language::find('cant_unlock_next');
        } else if ($is_admin || $is_to_confirm) {
            $html .= HTML::set_button($btn_title, '', URL::create_url('visit_lock'));
        }
    } else if ($type == Visit::CONFIRM_TYPECHECK) {
        $html .= HTML::set_button(strtoupper(Language::find($is_to_confirm ? 'confirm' : 'delete').' '.Language::find('quality_check')), '', URL::create_url('visit_lock'));
    } 
    URL::changeable_vars_from_onload_vars();
    return $html;
}


//-----------------------------------ROWS
function set_confirm_row() {
    global $oVisit, $html, $is_visit_completed, $is_visit_locked, $forms_uncompleted;
    $tds = '';
    $text = '';
    $group = '';
    if ($is_visit_locked) {
        $group = set_group('confirm_group', $group);
        $oAuthor = new User();
        $oAuthor->get_by_id($oVisit->lock_author);
        $temp = Language::find('visit_locked');
        $temp = str_replace('{0}', $oAuthor->name . ' ' . $oAuthor->surname, $temp);
        $temp = str_replace('{1}', Date::default_to_screen($oVisit->lock_ludati, false), $temp);
        $text .= $temp;

        URL::changeable_vars_reset();
        URL::changeable_var_add('pid', $oVisit->id_paz);
        URL::changeable_var_add('vid', $oVisit->id);
        if ($oVisit->has_output) {
            $text .= HTML::set_button(Icon::set_output() . Language::find('output'), '', URL::create_url('output'));
        }
        $text .= HTML::BR . HTML::BR . set_confirm_button(Visit::CONFIRM_TYPE_VLOCK, false);
        $tds .= HTML::set_td($text, 3, false);
    } else { 
        if ($is_visit_completed) {
            $group = set_group('confirm_group', $group);
            $text .= '<b>'.Language::find('can_confirm').'</b>';
            $text .= HTML::BR . HTML::BR . set_confirm_button(Visit::CONFIRM_TYPE_VLOCK, true);
        } else {
            $text .= '<b>' . $forms_uncompleted . Language::find('forms_uncompleted').'</b>';
            $html .= '<b style="margin-left: 10px;">' . $forms_uncompleted . Language::find('forms_uncompleted').'</b>';
        }
        $tds .= HTML::set_td($text, 3, false);
    }
    return $group . HTML::set_tr($tds);
}

function set_check_row() {
    global $oVisit, $is_visit_locked;
    $group = '';
    $trs = '';
    if ($is_visit_locked) {
        $group = set_group('quality_check', $group);
        $text = '';
        if ($oVisit->is_check) {
            $oAuthor = new User();
            $oAuthor->get_by_id($oVisit->check_author);
            $temp = Language::find('visit_checked');
            $temp = str_replace('{0}', $oAuthor->name . ' ' . $oAuthor->surname, $temp);
            $temp = str_replace('{1}', Date::default_to_screen($oVisit->check_ludati, false), $temp);
            $text .= $temp;
            if (isset($oVisit->check_note) && $oVisit->check_note . '' != '') {
                $text .= HTML::BR . '<b>'.Language::find('note').':</b> ' . $oVisit->check_note;
            }
            $text .= HTML::BR . HTML::BR . set_confirm_button(Visit::CONFIRM_TYPECHECK, false);
        } else {
            //-------------------ADD EVENTS CHECK
            $text .= set_confirm_button(Visit::CONFIRM_TYPECHECK, true);
        }
        $trs .= HTML::set_tr(HTML::set_td($text, 3, false));
    }
    return $group . $trs;
}


function set_unlockstatus_row() {
    global $oVisit;
    $trs = '';
    $text = '';
    if ($oVisit->unlock_reason . '' != '') {
        $oAuthor = new User();
        $oAuthor->get_by_id($oVisit->unlock_author);
        $temp = Language::find('visit_unlocked');
        $temp = str_replace('{0}', $oAuthor->name . ' ' . $oAuthor->surname, $temp);
        $temp = str_replace('{1}', Date::default_to_screen($oVisit->unlock_ludati, false), $temp);
        $text .= $temp;
        if (isset($oVisit->unlock_reason) && $oVisit->unlock_reason . '' != '') {
            $text .= HTML::BR . '<b>'.Language::find('note').':</b> ' . $oVisit->unlock_reason;
        }
        $trs .= HTML::set_tr(HTML::set_td($text, 3, false));
    }
    return $trs;
}
