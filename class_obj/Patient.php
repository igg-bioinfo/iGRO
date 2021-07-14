<?php

class Patient {

    public $id = 0;
    public $oCenter = NULL;
    public $patient_id = '';
    public $export_id = '';
    public $gender = 0;
    public $gender_txt = '';
    public $oDiagnosis = NULL;

    public $ethnicity = 0;
    public $ethnicity_text = '';
    public $ethnicity_other = '';

    public $date_onset = NULL;
    public $date_diagnosis = NULL;
    public $date_first_visit = NULL;
    private $age_base = NULL;

    
    public $id_diagnosis = NULL;
    public $dia_name = NULL;
    public $dia_short = NULL;
    public $dia_provisional = NULL;
    
    public $date_end = NULL;
    public $id_end_reason = 0;
    public $end_reason_txt = NULL;
    public $end_note = NULL;
    public $end_author = 0;
    public $end_ludati = NULL;
    public $end_specify = NULL;

    public $first_name = '';
    public $last_name = '';
    public $date_birth = NULL;
    public $country_birth = '';
    public $country_birth_other = '';

    public $sha_first_name = '';
    public $sha_last_name = '';
    public $sha_date_birth = '';
    public $sha_country_birth = '';
    public $sha_country_birth_other = '';

    public $visits = 0;
    public $visits_confirmed = 0;

    public $author = 0;
    public $ludati = NULL;
    public $last_visit = NULL;
    public $last_sched_visit = NULL;

    CONST NAT_NOT_AVAILABLE = 'Not available';

    //-----------------------------------------------------CONSTRUCT & INTERFACE METHODS
    function get_by_id($id) {
        $sql = "SELECT * FROM " . self::get_default_select() . " P where P.id_paz = ?";
        $params = [$id];
        $pt_rows = Database::read($sql, $params);
        if (count($pt_rows) > 0) {
            $this->set_by_row($pt_rows[0]);
            $this->decrypt();
        }
    }

    public function set_by_row($patient) {
        $this->id = $patient["id_paz"];
        $this->patient_id = $patient["patient_id"];
        $this->export_id = $patient["export_id"];
        $this->gender = $patient["gender"];
        $this->ethnicity = $patient["ethnicity"];
        $this->ethnicity_other = $patient["ethnicity_other"];

        $this->date_onset = $patient["date_onset"];
        $this->date_diagnosis = $patient["date_diagnosis"];
        $this->date_first_visit = $patient["date_first_visit"];
        $this->age_base = $patient["age_base"];

        $this->id_diagnosis = $patient["id_diagnosis"];
        $this->dia_name = $patient["dia_name"];
        $this->dia_short = $patient["dia_short"];
        $this->dia_provisional = $patient["dia_is_provisional"];

        $this->visits = $patient["visits"];
        $this->visits_confirmed = $patient["visits_confirmed"];

        $this->date_end = $patient["date_end"];
        $this->id_end_reason = $patient["id_end_reason"];
        $this->end_specify = isset($patient['end_specify']) ? $patient['end_specify'] : NULL;
        $this->end_reason_txt = $this->id_end_reason == End_reason::OTHER ? $patient['end_specify'] : End_reason::get_text($this->id_end_reason);
        $this->end_note = isset($patient['end_note']) ? $patient['end_note'] : NULL;
        $this->end_author = $patient["end_author"];
        $this->end_ludati = $patient["end_ludati"];

        if (!isset($this->oCenter) || $this->oCenter->id == 0) {
            $this->oCenter = new Center();
            $this->oCenter->get_by_id($patient['id_center']);
        }

        $this->sha_first_name = $patient["first_name"];
        $this->sha_last_name = $patient["last_name"];
        $this->sha_date_birth = $patient["date_birth"];
        $this->sha_country_birth = $patient["country_birth"];
        $this->sha_country_birth_other = $patient["country_birth_other"];
        $this->author = $patient["author"];
        $this->ludati = $patient["ludati"];
        $this->decrypt();
    }

    function census_create() {
        global $oUser;
        $this->new_patient_id();
        $this->new_export_id();
        if ($this->encrypt()) {
            $params = [$this->oCenter->id, $this->patient_id, $this->export_id, $this->gender, $this->date_onset, $this->date_diagnosis, 
                $this->date_birth, $this->date_end, $this->id_end_reason, $this->ethnicity, $this->ethnicity_other, $this->date_first_visit, $this->id_diagnosis, 
                $this->sha_first_name, $this->sha_last_name, $this->sha_country_birth, $this->sha_country_birth_other, $this->sha_date_birth, 
                $oUser->id];
            //age_base is calculated by the sql insert or update
            $this->id = Database::edit('INSERT INTO patient (id_center, patient_id, export_id, gender, date_onset, date_diagnosis,
                age_base, date_end, id_end_reason, ethnicity, ethnicity_other, date_first_visit, id_diagnosis, 
                first_name, last_name, country_birth, country_birth_other, date_birth, 
                author, ludati) VALUES (
                ?, ?, ?, ?, ?, ?, 
                DATEDIFF(NOW(), ?)/365.25, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, 
                ?, NOW());', $params, true);
        }
    }

    function census_update() {
        global $oUser;
        if ($this->encrypt()) {
            $params = [$this->gender, $this->date_onset, $this->date_diagnosis, $this->id_diagnosis, 
                $this->date_birth, $this->date_end, $this->id_end_reason, $this->ethnicity, $this->ethnicity_other, $this->date_first_visit,
                $this->sha_first_name, $this->sha_last_name, $this->sha_country_birth, $this->sha_country_birth_other, $this->sha_date_birth, 
                $oUser->id, $this->id];
            //age_base is calculated by the sql insert or update
            Database::edit('UPDATE patient SET gender = ?, date_onset = ?, date_diagnosis = ?, id_diagnosis = ?, 
                age_base = DATEDIFF(NOW(), ?)/365.25, date_end = ?, id_end_reason = ?, ethnicity = ?, ethnicity_other = ?, date_first_visit = ?,
                first_name = ?, last_name = ?, country_birth = ?, country_birth_other = ?, date_birth = ?, 
                author = ?, ludati = NOW() WHERE id_paz = ?;', $params, '', false);
        }
    }

    function update_discontinuation() {
        global $oUser;
        $params = [$this->date_end, $this->id_end_reason, $this->end_specify, $this->end_note, $oUser->id, $this->id];
        Database::edit('UPDATE patient SET date_end = ?, id_end_reason = ?, end_specify = ?, end_note = ?, end_author = ?, end_ludati = NOW() WHERE id_paz = ?;', $params, '', false);
    }

    public function update_provisional($is_provisional) {
        $this->is_provisional = $is_provisional;
        $sql = "UPDATE patient SET dia_is_provisional = ? WHERE id_paz = ? ";
        $params = [$this->is_provisional ? 1 : 0, $this->id_paz];
        Database::edit($sql, $params);
    }

    function delete($note = '') {
        global $oUser;
        if (isset($this->id) && $this->id != 0 && isset($oUser) && $oUser->id != 0) {
            $this->set_encrypted();
            $sql = "INSERT INTO patient_deleted (id_paz, note, patient_json, author, ludati) VALUES (?, ?, ?, ?, NOW());
                DELETE FROM patient WHERE id_paz = ?; ";
            $params = [$this->id, $note, json_encode($this), $oUser->id, $this->id];
            Database::edit($sql, $params);
        }
    }

    private function new_export_id() {
        $new_id = Security::random(8);
        $check_id = Database::read("SELECT id_paz FROM patient WHERE export_id = ?", [$new_id]);
        if (count($check_id) > 0) {
            $this->new_export_id();
            return;
        }
        $this->export_id = strtoupper($new_id);
    }

    //-----------------------------------------------------PUBLIC
    function is_discontinued($date = NULL) {
        return isset($this->date_end) && (!isset($date) || Date::date_difference_in_days($date, $this->date_end) > 0);
    }

    function get_general_discontinuation_detail($carriage_return = true) {
        $detail = Language::find('end_details');
        $detail = str_replace('%%%', Date::default_to_screen($this->date_end), $detail);
        $detail = str_replace('$$$', strtolower($this->end_reason_txt), $detail);
        if (!$carriage_return) { $detail = str_replace('<br>', '', $detail); }
        return $detail;
    }

    function is_dead($date = NULL) {
        return $this->id_end_reason == End_reason::DEATH['id'] && isset($this->date_end) && (!isset($date) || Date::date_difference_in_days($date, $this->date_end) > 0);
    }

    public function get_age($date = NULL) {
        if (is_null($date)) {
            $date = new DateTime('now');
        } else if (!is_a($date, 'DateTime')) {
            $date = Date::default_to_object($date);
        }
        if (!is_a($date, 'DateTime')) {
            throw new Exception('Parameter must be DateTime!');
        }
        $last_update = Date::default_to_object($this->ludati);
        $diff = $last_update->diff($date);
        if ($diff->invert) { //if the difference is negative
            return round($this->age_base - ($diff->days / 365.25), 1);
        } else {
            return round($this->age_base + ($diff->days / 365.25), 1);
        }
    }

    /*
    public function calculate_next_visit_warning(&$warning_level) {
        if (!isset($this->last_visit)) {
            $warning_level = 1;
            return Icon::set_with_tooltip('warning', Label::NO_DATES, 2, '', 'color: ' . Label::NO_DATES_COLOR);
        }
        $now = new DateTime('now');
        if (($now >= $this->target_date_min) && ($now <= $this->target_date_max)) {
            $warning_level = 3;
            return Icon::set_with_tooltip('warning', Label::VISIT_IS_RANGE, 2, '', 'color: ' . Label::VISIT_IS_RANGE_COLOR);
        } else if ($now < $this->target_date_min) {
            $diff_with_min = date_diff($this->target_date_min, $now);
            if ($diff_with_min->days < 28) {
                $warning_level = 2;
                return Icon::set_with_tooltip('warning', Label::INCOMING_VISIT, 2, '', 'color: ' . Label::INCOMING_VISIT_COLOR);
            }
        } else if ($now > $this->target_date_max) {
            $warning_level = 4;
            return Icon::set_with_tooltip('warning', Label::OUT_OF_WINDOW_VISIT, 2, '', 'color: ' . Label::OUT_OF_WINDOW_VISIT_COLOR);
        }
    }
    */
    //-----------------------------------------------------PRIVATE
    private function new_patient_id() {
        $new_id = 1;
        $check_id = Database::read("SELECT patient_id FROM patient WHERE id_center = ? ORDER BY id_paz desc", [$this->oCenter->id]);
        if (count($check_id) > 0) {
            $new_id = $check_id[0]["patient_id"] . '' != '' ? substr($check_id[0]["patient_id"], -5, 5) + 1 : 1;
        }
        $this->patient_id = $this->oCenter->code . Strings::add_zeros($new_id, 5);
    }

    private function encrypt() {
        global $oUser;
        if (isset($oUser->oCenter) && $oUser->oCenter->has_password()) {
            $pw = $oUser->oCenter->get_password();
            $this->sha_first_name = Encryption::encrypt($this->first_name, $pw);
            $this->sha_last_name = Encryption::encrypt($this->last_name, $pw);
            $this->sha_country_birth = Encryption::encrypt($this->country_birth, $pw);
            $this->sha_country_birth_other = Encryption::encrypt($this->country_birth_other, $pw);
            $this->sha_date_birth = Encryption::encrypt($this->date_birth, $pw);
            return true;
        } else {
            return false;
        }
    }

    public function set_encrypted() {
        $this->first_name = "Encrypted";
        $this->last_name = "Encrypted";
        $this->country_birth = "Encrypted";
        $this->country_birth_other = "Encrypted";
        $this->date_birth = "Encrypted";
    }

    public function decrypt() {
        global $oUser;
        if (isset($oUser->oCenter) && $oUser->oCenter->has_password()) {
            $pw = $oUser->oCenter->get_password();
            $this->first_name = Encryption::decrypt($this->sha_first_name, $pw);
            $this->last_name = Encryption::decrypt($this->sha_last_name, $pw);
            $this->country_birth = Encryption::decrypt($this->sha_country_birth, $pw);
            $this->country_birth_other = Encryption::decrypt($this->sha_country_birth_other, $pw);
            $this->date_birth = Encryption::decrypt($this->sha_date_birth, $pw);
        } else {
            $this->set_encrypted();
        }
    }

    static function get_all_removed() {
        return Database::read("SELECT id_paz AS id, note, author, ludati FROM patient_deleted WHERE id_paz IS NOT NULL ORDER BY ludati DESC", []);
    }

    public static function get_default_select() {
        return "( select P.*, IC.center_code, IC.hospital, D.dia_name, D.dia_short, 
            IFNULL(visits, 0) AS visits, IFNULL(visits_confirmed, 0) AS visits_confirmed  
            FROM patient P
            INNER JOIN center IC ON IC.id_center = P.id_center
            INNER JOIN diagnosis D ON D.id_dia = P.id_diagnosis
            LEFT OUTER JOIN (SELECT id_paz, COUNT(id_visita) as visits FROM visit GROUP BY id_paz) VS ON VS.id_paz = P.id_paz
            LEFT OUTER JOIN (SELECT id_paz, COUNT(id_visita) as visits_confirmed FROM visit WHERE is_lock = 1 GROUP BY id_paz) VC ON VC.id_paz = P.id_paz
        )";
    }

    public function update_end_note() {
        $params = [$this->end_note, $this->id];
        Database::edit("UPDATE patient SET end_note = ? WHERE id_paz = ? ", $params);
    }

    public function has_visits() {
        return $this->visits > 0;
    }

    public function has_visits_confirmed() {
        return $this->visits_confirmed > 0;
    }

    public function has_visits_not_confirmed() {
        return $this->visits > $this->visits_confirmed;
    }


}
