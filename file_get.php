<?php

$file_id = URL::get_onload_var(File::ENCRYPTED_URL_ID);
$file_type = URL::get_onload_var(File::ENCRYPTED_URL_TYPE);
$file_link_date = URL::get_onload_var(File::ENCRYPTED_URL_DATE);

//--------------------------------------------CHECK TYPE
if ($file_id == '' || $file_type == '' || $file_link_date == '') {
    URL::redirect('error', 1);
}

//--------------------------------------------CHECK TYPE
/*
switch ($file_type) {
    case File::ENCRYPTED_TYPE_TICKET:
        if (!$oUser->is_logged()) {
            URL::redirect('error', 1);
        }
        Ticket::set_file_properties();
        break;
    case File::ENCRYPTED_TYPE_INFORMED_CONSENT:
        if (!$oUser->is_logged()) {
            URL::redirect('error', 1);
        }
        Informed_consent::set_file_properties();
        break;
    case File::ENCRYPTED_TYPE_PROJECT_DOC:
        if (!$oUser->is_logged()) {
            URL::redirect('error', 1);
        }
        Abstract_project::set_file_properties();
        break;
}
*/

//--------------------------------------------CHECK ENCRYPTION
if (!File::$is_encrypted) {
    URL::redirect('error', 1);
}

//--------------------------------------------CHECK LINK LIFETIME
//echo 'date_start = ' . $file_link_date . '<br>';
$file_link_date = Date::screen_to_object($file_link_date, true);
$date = new DateTime();
$minutes = Date::date_difference($file_link_date, $date, Date::INTERVAL_MINS);
//echo 'date_now = ' . Date::object_to_screen($date, true) . '<br>';
//echo 'minutes_diff = ' . $minutes . '<br>';
//exit;
if (File::$encrypted_min_link_life != 0 && $minutes > File::$encrypted_min_link_life) {
    URL::redirect('error', 1);
}

//--------------------------------------------GET FILE
$oFile = NULL;
$sql = "SELECT * FROM FILE_ENCRYPTED WHERE id_file = ? AND file_encrypted_type = ?; ";
$params = [$file_id, File::$encrypted_type];
$file = Database::read($sql, $params);
if (count($file) > 0) {
    $oFile = new File($file[0]['file_name']);
}
if (!isset($oFile)) {
    URL::redirect('error', 1);
}

//--------------------------------------------CHECK FILE
if (!$oFile->check_extension() || !$oFile->check_size()) {
    URL::redirect('error', 1);
}

//--------------------------------------------GET CONTENTS
$contents = $oFile->decrypt();
if (!$contents) {
    URL::redirect('error', 1);
}

//--------------------------------------------SET HEADER & CONTENTS
/* echo $contents;
  echo '<br>';
  echo '<br>';
  fpassthru($contents);
  exit; */
header('Content-Type: ' . File_exts::get_content_type($oFile->extension));
header('Content-disposition: filename="' . $oFile->filename . '"');
echo $contents;
exit;
