<?php

function set_flow(&$flow) {
    global $oUser;
    if (!$oUser->is_logged()) {
        header("HTTP/1.0 404 Not Found");
        echo 'Session expired';
        exit;
    }
    $session_logged = Database::read("SELECT id_user FROM session WHERE id_user = ? AND id_area = ? AND ip_address = ?; ",
        [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
    if (count($session_logged) == 0) {
        Database::read("INSERT INTO session (id_user, id_area, ip_address, date_login, last_seen) VALUES (?, ?, ?, NOW(), NOW()); ",
            [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
    } else {
        Database::read("UPDATE session SET last_seen = NOW() WHERE id_user = ? AND id_area = ? AND ip_address = ?; ",
            [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
    }
    $flow_type_act = URL::get_onload_var('fta');
    $filename = URL::get_onload_var('nm');
    $folder = URL::get_onload_var('fd');
    $exts = json_decode(URL::get_onload_var('exts'));
    $max = URL::get_onload_var('max');
    $flow = new File_flow($folder, $filename);
    $flow->extensions_allowed = is_array($exts) ? $exts : [];
    $flow->size_bytes_max = File::get_size_bytes_by_MB($max);
    $flow->do_action($flow_type_act);
}

switch (URL::get_onload_var('fn')) {

    //-------SESSION
    case Globals::AJAX_SESSION:
        $sql = "BEGIN NOT ATOMIC
            INSERT INTO session_archive (id_user, id_area, ip_address, date_login, date_logout)
            SELECT id_user, id_area, ip_address, date_login, DATE_ADD(last_seen, INTERVAL '" . Globals::SESSION_MAX_MIN . "' MINUTE)
            FROM session WHERE TIMESTAMPDIFF(MINUTE, last_seen, NOW()) > " . Globals::SESSION_MAX_MIN . ";

            DELETE FROM session WHERE TIMESTAMPDIFF(MINUTE, last_seen, NOW()) > " . Globals::SESSION_MAX_MIN . ";
            END;";
        Database::edit($sql, []);

        $sql = "SELECT id_user FROM session WHERE id_user = ? AND id_area = ? AND ip_address = ?;";
        $session = Database::read($sql, [$oUser->id, &$oUser->oAccessedArea->id, &$oUser->ip_address]);
        //echo $oUser->id.' '.$oArea->id.' '.$oUser->ip_address;
        if (count($session) == 0) {
            $oUser->logout();
        }
        break;

    //-------VISIT TYPE
    case Globals::AJAX_VISIT_TYPE:
        Language::add_area('visit');
        $json = '{ "query": "Unit", ';
        if (!$oUser->is_logged()) {
            $json .= ' "error": "'.Language::find('access_denied').'"" }';
            echo $json;
            exit();
        }
        $date_input_screen = Security::sanitize(INPUT_POST, 'date');
        if ($oPatient->id == 0 && $oVisit->id == 0) { // && $date_visit.'' != ''
            $json .= ' "error": "'.Language::find('params_error').'" }';
            echo $json;
            exit();
        }
        if (Config::PROJECT_CLOSED) {
            $json .= ' "error": "'.Language::find('study_closed').'"}';
            echo $json;
            exit();
        }
        $date_input_object = Date::screen_to_object($date_input_screen, false);
        $is_new_date = $date_input_object != Date::default_to_object($oVisit->date, false);
        $is_new = ($oVisit->id == 0);
        $oLastVisit = new Visit();
        if ($is_new) {
            if ($oPatient->is_discontinued($date_input_object)) {
                $json .= ' "error": "'.Language::find('params_error', ['patient']).' (' . Date::default_to_screen($oPatient->date_end) . ')"}';
                echo $json;
                exit();
            }
            $oLastVisit->get_last($oPatient->id);
            if ($oLastVisit->id != 0 && Date::default_to_object($oLastVisit->date, false) >= $date_input_object) {
                $json .= ' "error": "' . str_replace('%%%', $date_input_screen, Language::find('date_before_last')) . '"}';
                echo $json;
                exit();
            }
        } else {
            $oLastVisit->get_previous($oPatient->id, $oVisit->date);
            if ($oLastVisit->id != 0 && Date::default_to_object($oLastVisit->date, false) >= $date_input_object) {
                $json .= ' "error": "' . str_replace('%%%', $date_input_screen, Language::find('date_before_prev')) . '"}';
                echo $json;
                exit();
            }
            $oNextVisit = new Visit();
            $oNextVisit->get_next($oPatient->id, $oVisit);
            if ($oNextVisit->id != 0 && $date_input_object >= Date::default_to_object($oNextVisit->date, false)) {
                $json .= ' "error": "' . str_replace('%%%', $date_input_screen, Language::find('date_after_next')) . '"}';
                echo $json;
                exit();
            }
        }

        //DATE VISIT BEFORE ENROLL
        if ($date_input_object < Date::default_to_object($oPatient->date_first_visit, false)) {
            $json .= ' "error": "' . str_replace('%%%', $date_input_screen, Language::find('date_before_first')) . ' (' . Date::default_to_screen($oPatient->date_first_visit) . ')."}';
            echo $json;
            exit();
        }

        
        //VISIT TYPES
        $oVisit_types = Visit_type::get_all($oPatient->id, $oVisit->id);
        if (count($oVisit_types) == 0) {
            $json .= ' "error": "'.Language::find('no_type').'"}';
            echo $json;
            exit();
        } else {
            for($o = 0; $o < count($oVisit_types); $o++) {
                $oVisit_types[$o]->name = $oVisit_types[$o]->code.' - '.$oVisit_types[$o]->get_name();
            }
        }


        //IS VIEW
        $is_view = $oPatient->is_discontinued($date_input_object) || $oVisit->is_lock || Config::PROJECT_CLOSED;
        $json .= ' "is_view": ' . ($is_view ? 'true' : 'false') . ', ';


        //SUGGESTED TYPE
        $oSuggested_type = NULL;
        if ($is_new || $is_new_date) {
            $days = Date::date_difference_in_days(Date::default_to_object($oPatient->date_first_visit), $date_input_object);
            $oSuggested_type = Visit_type::suggests($days, $oVisit_types);
        } else {
            $oSuggested_type = new Visit_type();
            $oSuggested_type->get_by_id($oVisit->type_id);
            $oSuggested_type->name = $oSuggested_type->code.' - '.$oSuggested_type->get_name();
            $days = Date::date_difference_in_days(Date::default_to_object($oPatient->date_first_visit), $date_input_object);
            $oSuggested = Visit_type::suggests($days, $oVisit_types);
            if ($oSuggested_type->id != $oSuggested->id) {
                $found = false;
                foreach($oVisit_types as $oVisit_type) {
                    if ($oVisit_type->id == $oSuggested_type->id) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $oVisit_types[] = $oSuggested_type;
                }
            }
        }
        if (isset($oSuggested_type)) {
            $json .= ' "suggested": ' . json_encode($oSuggested_type) . ', ';
        }

        $json .= ' "vtypes": ' . json_encode($oVisit_types) . ' ';
        $json .= ' }';
        echo $json;
        break;


    //--------UPLOAD FILE
    case Globals::AJAX_FLOW_FILE_BASIC:
        $flow = NULL;
        set_flow($flow);
        $flow->message_and_exit();
        break;
}
exit();
