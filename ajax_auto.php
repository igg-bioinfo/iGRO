<?php

// Check if user sending AJAX request is logged on
$ajax_type = Security::sanitize(INPUT_GET, 'at', FILTER_SANITIZE_STRING);
$ajax_value = Security::sanitize(INPUT_GET, 'av', FILTER_SANITIZE_STRING);
if (!$oUser->is_logged() || $ajax_type == '' || $ajax_value == '') {
    $json = '{ "error": "access_denied" }';
    echo $json;
    exit();
}

switch ($ajax_type) {


    case Globals::AJAX_AUTO_ADD_EXTERNAL:
        $sql = "SELECT DISTINCT U.id_med AS " . Ajax_autocompleter::AUTO_ID . ", U.name + ' ' + U.surname AS " . Ajax_autocompleter::AUTO_LABEL . "
            FROM " . User::get_table(false) . " U
            WHERE " . Ajax_autocompleter::SQL_REPLACE_WHERE . " ";
        $responder = new Ajax_autocompleter($sql, "U.name + ' ' + U.surname");
        $responder->run();
        break;

    case Globals::AJAX_AUTO_FILTER_EXTERNAL:
        $sql = "SELECT DISTINCT IE.id_external AS " . Ajax_autocompleter::AUTO_ID . ", U.name + ' ' + U.surname AS " . Ajax_autocompleter::AUTO_LABEL . "
            FROM " . User::get_table(false) . " U
            INNER JOIN INFO_EXTERNAL IE ON U.id_med = IE.id_med
            WHERE " . Ajax_autocompleter::SQL_REPLACE_WHERE . " ";
        $responder = new Ajax_autocompleter($sql, "U.name + ' ' + U.surname");
        $responder->run();
        break;
}