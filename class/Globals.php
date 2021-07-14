<?php

//--------------------------------GLOBALS
class Globals {
    const LOGIN_ATTEMPT = 5;
    const MAX_PASSWORD_DURATION = 90;
    const ICON_ERROR = "fa fa-times fa-2x testorosso";
    const ICON_CHECKED = "fa fa-check fa-2x testoverde";
    const ICON_WARNING = "fa fa-exclamation-triangle fa-2x testoarancio";
    const LOGIN_LINK_EXPIRED_MIN = 60;
    const SESSION_MAX_MIN = 60;
    const AJAX_SESSION = 1;
    const AJAX_VISIT_TYPE = 2;
    const AJAX_DRUG_UM_ROUTE = 3;
    const AJAX_PT_CODE_DUPLICATED = 4;
    const AJAX_FORM_LIST = 5;
    const AJAX_FLOW_FILE_BASIC = 6;
    const AJAX_AUTO_ADD_EXTERNAL = 'addextuser';
    const AJAX_AUTO_FILTER_EXTERNAL = 'filterextuser';
    const FORM_CLASS_FILE = "crf/form_manager.php";
    const FORM_URL = "form";
    const SPECIAL_CHARACTERS = ['!', '#', '$', '%', '&', '*', '+', ',', '.', '/', ':', ';', '=', '?', '@', '\\', '^', '_', '`', '|', '~'];

    public static $PATH_RELATIVE = Config::PATH_RELATIVE;
    public static $PHYSICAL_PATH = Config::PHYSICAL_PATH;
    public static $DOMAIN_URL = Config::DOMAIN_URL;
    public static $URL_RELATIVE = Config::URL_RELATIVE;

}

//--------------------------------HTTPS
if (Config::USE_HTTPS) {
    if ($_SERVER["HTTPS"] != "on") {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }

    $currentCookieParams = session_get_cookie_params();
    if (!$currentCookieParams["httponly"] || !$currentCookieParams["secure"]) {
        session_set_cookie_params($currentCookieParams["lifetime"], Globals::$URL_RELATIVE, $_SERVER["SERVER_NAME"], true, true);
    }
}

//-------------------------------- OTHER PRODUCTION SPECIFIC RULES
if (Config::SITEVERSION != 'TEST') {
    // empty
}

session_cache_limiter('nocache');

//--------------------------------AUTOLOAD
function include_class($class_name, $folder) {
    $class_file = Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . $folder . Config::PATH_SEP . $class_name . '.php';
    if ($class_name != 'Globals' && file_exists($class_file)) {
        include_once $class_file;
    }
}

spl_autoload_register(function ($class_name) {
    include_class($class_name, 'class');
    include_class($class_name, 'class_obj');
    include_class($class_name, 'class_overrides'); //Config::PATH_SEP x sottocartelle
});


//-------------------------------DEFAULT DATE & SESSION START
date_default_timezone_set("Europe/Rome");
