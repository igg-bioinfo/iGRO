<?php

class Mailer {

    private $mailer = NULL;
    private $subject = '';
    private $oSender = NULL;
    private $body = '';
    private $oNotSent = [];
    private $archive_addresses = '';
    public $debugger = '';

    const SESSION_NOT_SENT = 'Email_not_sent';
    const VAR_NAME = '%name%';
    const VAR_VISIT_TYPE = '%visit_type%';
    const VAR_DATE = '%date%';
    const VAR_PTCODE = '%pt_code%';
    const VAR_RESPONSE = '%response%';

    //-----------------------------------------------------CONSTRUCT
    public function __construct() {
        include_once Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . 'PHPMailer'.Config::PATH_SEP.'src'.Config::PATH_SEP.'PHPMailer.php';
        include_once Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . 'PHPMailer'.Config::PATH_SEP.'src'.Config::PATH_SEP.'SMTP.php';
        include_once Globals::$PHYSICAL_PATH . Globals::$PATH_RELATIVE . 'PHPMailer'.Config::PATH_SEP.'src'.Config::PATH_SEP.'Exception.php';
        $this->mailer = new PHPMailer\PHPMailer\PHPMailer();
        Language::add_area('email');
        //---------- ENCONDING FOR 75TH CHAR ISSUE
        $this->mailer->Encoding = 'base64';
        if (Config::EMAIL_SMTP != '') {
		    $this->mailer->IsSMTP();
		    $this->mailer->SMTPAuth = true;
		    $this->mailer->SMTPKeepAlive = true;
		    $this->mailer->SMTPSecure = Config::EMAIL_SMTP_PORT === 587 ? 'tls' : PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
		    $this->mailer->Port = Config::EMAIL_SMTP_PORT;
		    $this->mailer->Host = Config::EMAIL_SMTP; 
		    $this->mailer->Username = Config::EMAIL_ADMIN;
		    $this->mailer->Password = Config::EMAIL_SMTP_PW;
        }
        $this->mailer->CharSet = 'UTF-8';
    }

    //-----------------------------------------------------PRIVATE
    private function set_error() {
        Error_log::$code = 0;
        Error_log::$file = 'class\Mailer.php';
        $error = 'the email was not sent to: ';
        foreach ($this->oNotSent as $oNotSent) {
            $error .= $oNotSent->id . ' - ' . $oNotSent->email . '; ';
        }
        Error_log::$message = $error;
        Error_log::set('EMAIL', false);
    }

    private function set_oSender() {
        if (!isset($this->oSender)) {
            $this->oSender = self::get_admin(false);
        }
        $this->mailer->From = $this->oSender->email;
        $this->mailer->FromName = $this->oSender->name . ($this->oSender->name != '' ? ' ' . $this->oSender->surname : '');
    }

    private function archive() {
        global $oUser;
        $sql = "INSERT INTO mail_archive (mail_sender, mail_subject, mail_body, mail_addresses, author, ludati)
            VALUES (?, ?, ?, ?, ?, NOW())";
        $params = [$this->oSender->email, $this->subject, $this->body, $this->archive_addresses, $oUser->id];
        Database::edit($sql, $params);
    }

    //-----------------------------------------------------PUBLIC
    public function set_subject($subject) {
        $this->subject = $subject;
    }

    public function set_sender($oUser) {
        $this->oSender = $oUser;
    }

    public function set_message($message, $is_html = true) {
        if ($is_html) {
            $this->mailer->IsHTML(true);
            $message = str_replace('\n\r', '<br>', $message);
            $message = str_replace('\n', '<br>', $message);
            $message = str_replace('\r', '<br>', $message);
        } else {
            $message = str_replace('<br>', '\n', $message);
            $message = str_replace('<br />', '\n', $message);
            $message = str_replace('<br/>', '\n', $message);
        }
        $this->body = $message;
    }

    //-----------------------------------------------------SEND
    public function send($oRecipients, $to_archive = false, $is_debug = false) {
        $this->debugger = '';
        $this->archive_addresses = '';
        $is_localhost = Security::sanitize(INPUT_SERVER, 'SERVER_NAME') == 'localhost';
        if (!isset($this->subject) || $this->subject . '' == '') {
            return false;
        }
        if (!isset($this->body) || $this->body . '' == '') {
            return false;
        }

        $this->mailer->Subject = (Config::SITEVERSION == "" ? "" : "[" . Config::SITEVERSION . "] ") .Config::TITLE.' - '. $this->subject;
        $this->set_oSender();
        if ($is_debug) {
            $this->debugger .= '<b>FROM</b>' . HTML::BR;
            $this->debugger .= $this->mailer->FromName . ' ' . $this->mailer->From . HTML::BR;
            $this->debugger .= '<b>SUBJECT</b>' . HTML::BR;
            $this->debugger .= $this->mailer->Subject . HTML::BR . HTML::BR;
        }

        $this->oNotSent = [];
        $this->mailer->clearAddresses();
        foreach ($oRecipients as $oRecipient) {
            if (!isset($oRecipient->email) || $oRecipient->email . '' == '' || !isset($oRecipient->name) || $oRecipient->name . '' == '') {
                $this->oNotSent[] = $oRecipient;
                continue;
            }
            $name = $oRecipient->name . ($oRecipient->surname . '' != '' ? ' ' . $oRecipient->surname : '');
            $this->mailer->AddAddress($oRecipient->email, $name);
            $this->mailer->Body = str_replace(self::VAR_NAME, $name, $this->body);

            //-------------------SEND EMAIL
            if ($is_debug) {
                $this->debugger .= '<b>TO</b>' . HTML::BR;
                $this->debugger .= $name . ' ' . $oRecipient->email . HTML::BR;
                $this->debugger .= '<b>BODY</b>' . HTML::BR;
                $this->debugger .= $this->mailer->Body . HTML::BR . HTML::BR;
            } else if ($is_localhost) {
                error_log($this->mailer->Body);
            } else if (!$this->mailer->send()) {
                $this->oNotSent[] = $oRecipient;
                //echo !extension_loaded('openssl')?"Not Available":"Available";
                //var_dump($this->mailer);
                //exit;
            }
            $this->archive_addresses .= $oRecipient->email . ' ';

            $this->mailer->clearAddresses();
        }
        if ($to_archive) {
            $this->archive();
        }
        if ($is_debug) {
            echo $this->debugger;
            exit;
        }
        if (count($this->oNotSent) > 0) {
            $_SESSION[self::SESSION_NOT_SENT] = $this->oNotSent;
            $this->set_error();
            return false;
        }
        return true;
    }

    //-----------------------------------------------------STATIC
    public static function get_admin() {
        $oUser = new User();
        $oUser->name = Config::TITLE.' Administrator';
        $oUser->email = Config::EMAIL_ADMIN;
        return $oUser;
    }

    public static function get_msg_output() {
        $msg = "";
        if (Language::$iso == 'en') {
            $msg .= "Dear " . self::VAR_NAME . "," . HTML::BR . HTML::BR;
            $msg .= "Please find below the output for " . self::VAR_VISIT_TYPE . " visit";
            $msg .= " of " . self::VAR_DATE . " for subject " . self::VAR_PTCODE . ":";
            $msg .= HTML::BR . HTML::BR;
            $msg .= self::VAR_RESPONSE;
            $msg .= HTML::BR;
            $msg .= HTML::BR;
            $msg .= "Please note that the recommendations are based only on data received from the site. ";
            $msg .= "The treating physician is always required to assess subject safety and the impact of study medication";
            $msg .= " dosing on the patient as detailed in the protocol.";
            $msg .= HTML::BR;
            $msg .= HTML::BR;
            $msg .= "Kind regards,".HTML::BR."the Administrator";
        } else if (Language::$iso == 'it') {
            $msg .= "Gentile " . self::VAR_NAME . "," . HTML::BR . HTML::BR;
            $msg .= "A seguire l'output inerente alla visita in oggetto (" . self::VAR_VISIT_TYPE . " ";
            $msg .= " - data " . self::VAR_DATE . ") per il paziente " . self::VAR_PTCODE . ":";
            $msg .= HTML::BR . HTML::BR;
            $msg .= self::VAR_RESPONSE;
            $msg .= HTML::BR;
            $msg .= HTML::BR;
            $msg .= "Considera che le raccomandazioni si basano solamente sui dati ricevuti dal sito. ";
            $msg .= "Il parere del medico curante è sempre necessario per provvedere alla sicurezza del paziente e per valutare l'impatto ";
            $msg .= " che le medicazioni definite nel protocollo dello studio hanno sul paziente.";
            $msg .= HTML::BR;
            $msg .= HTML::BR;
            $msg .= "Cordiali saluti,".HTML::BR."l'Amministratore";
        }
        return $msg;
    }

}