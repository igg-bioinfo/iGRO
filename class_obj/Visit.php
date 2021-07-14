<?php

class Visit {

    public $id = 0;
    public $id_paz = 0;
    public $export_id = '';
    public $patient_id = '';
    public $date = NULL;
    public $age_visit = NULL;
    public $disease_duration = NULL;

    public $type_id = NULL;
    public $type = '';
    public $type_code = '';
    public $always_show = NULL;
    public $is_extra = NULL;
    public $has_output = NULL;
    public $has_randomization = NULL;

    public $day = NULL;
    public $day_lower = NULL;
    public $day_upper = NULL;

    public $author = NULL;
    public $ludati = NULL;

    public $is_lock = NULL;
    public $lock_author = NULL;
    public $lock_ludati = NULL;
    public $lock_reason = NULL;

    public $is_check = false;
    public $check_author = NULL;
    public $check_ludati = NULL;
    public $check_note = '';

    public $unlock_author = NULL;
    public $unlock_ludati = NULL;
    public $unlock_reason = NULL;
    public $unlock_note = '';

    const CONFIRM_TYPE_VLOCK = 'vlock';
    const CONFIRM_TYPECHECK = 'admincheck';

    //-----------------------------------------------------CONSTRUCT & INTERFACE METHODS
    function __construct($visit = []) {
        if (count($visit) > 0) {
            $this->set_by_row($visit);
        }
    }

    function get_by_id($id) {
        if ($id != 0) {
            Visit_type::$visit_list_mode = false;
        }
        $params = [$id];
        $sql = "SELECT VV.* FROM " . self::get_default_select() . " VV WHERE VV.id_visita = ? ";
        $visits = Database::read($sql, $params);
        $this->get_oVisit($visits);
    }

    public function set_by_row($visit) {
        $this->id = isset($visit["id_visita"]) ? $visit["id_visita"] : NULL;
        $this->id_paz = isset($visit["id_paz"]) ? $visit["id_paz"] : NULL;
        $this->export_id = isset($visit["export_id"]) ? $visit["export_id"] : NULL;
        $this->patient_id = isset($visit["patient_id"]) ? $visit["patient_id"] : NULL;
        $this->date = isset($visit["date_visit"]) ? $visit["date_visit"] : NULL;
        //$this->age_visit = isset($visit["age_visit"]) ? $visit["age_visit"] : NULL;
        //$this->disease_duration = isset($visit["disease_duration"]) ? $visit["disease_duration"] : NULL;

        $this->type_id = isset($visit["visit_type_id"]) ? $visit["visit_type_id"] : NULL;
        $this->type_code = isset($visit["visit_type_code"]) ? $visit["visit_type_code"] : NULL;
        $this->type = isset($visit["visit_type"]) ? Language::find($visit["visit_type"]) : NULL;
        $this->always_show = $visit["always_show"] . '' == '1';
        $this->is_extra = $visit["is_extra"] . '' == '1';
        $this->has_output = $visit["has_output"] . '' == '1';
        $this->has_randomization = $visit["has_randomization"] . '' == '1';

        $this->day = isset($visit["visit_day"]) ? $visit["visit_day"] : NULL;
        $this->day_lower = isset($visit["visit_day_lower"]) ? $visit["visit_day_lower"] : NULL;
        $this->day_upper = isset($visit["visit_day_upper"]) ? $visit["visit_day_upper"] : NULL;

        $this->author = isset($visit["visit_author"]) ? $visit["visit_author"] : NULL;
        $this->ludati = isset($visit["visit_ludati"]) ? $visit["visit_ludati"] : NULL;

        $this->is_lock = $visit["is_lock"] . '' == '1';
        $this->lock_author = isset($visit["lock_author"]) ? $visit["lock_author"] : NULL;
        $this->lock_ludati = isset($visit["lock_ludati"]) ? $visit["lock_ludati"] : NULL;
        $this->lock_reason = isset($visit["lock_reason"]) ? $visit["lock_reason"] : NULL;

        $this->is_check = $visit["is_check"] . '' == '1';
        $this->check_author = isset($visit["check_author"]) ? $visit["check_author"] : NULL;
        $this->check_ludati = isset($visit["check_ludati"]) ? $visit["check_ludati"] : NULL;
        $this->check_note = isset($visit["check_note"]) ? $visit["check_note"] : NULL;

        $this->unlock_author = isset($visit["unlock_author"]) ? $visit["unlock_author"] : NULL;
        $this->unlock_ludati = isset($visit["unlock_ludati"]) ? $visit["unlock_ludati"] : NULL;
        $this->unlock_reason = isset($visit["unlock_reason"]) ? $visit["unlock_reason"] : NULL;
        $this->unlock_note = isset($visit["unlock_note"]) ? $visit["unlock_note"] : NULL;
    }

    function create() {
        global $oUser;
        $params = [$this->id_paz, $this->date, $this->visit_type_id, 0, 0, $oUser->id];
        $sql = "INSERT INTO visit (id_paz, date_visit, visit_type_id, is_lock, is_check, author, ludati) VALUES (?, ?, ?, ?, ?, ?, NOW())";
        Database::edit($sql, $params);
        //$this->id = Database::edit($sql, $params, true);
    }

    function update() {
        global $oUser;
        $params = [$this->date, $this->visit_type_id, $oUser->id, $this->id];
        $sql = "UPDATE visit SET date_visit = ?, visit_type_id = ?, author = ?, ludati = NOW()  WHERE id_visita = ? ";
        Database::edit($sql, $params);
    }

    function update_lock() {
        global $oUser;
        $params = [1, $this->lock_reason, $oUser->id, $this->id];
        $sql = "UPDATE visit SET is_lock = ?, lock_reason = ?, lock_author = ?, lock_ludati = NOW(),
            unlock_reason = NULL, unlock_author = NULL, unlock_ludati = NULL
            WHERE id_visita = ? ";
        Database::edit($sql, $params);
    }

    function update_unlock() {
        global $oUser;
        $params = [0, $this->unlock_reason, $oUser->id, $this->id];
        $sql = "UPDATE visit SET is_lock = ?, unlock_reason = ?, unlock_author = ?, unlock_ludati = NOW(),
            is_check = 0, check_author = NULL, check_ludati = NULL
            WHERE id_visita = ? ";
        Database::edit($sql, $params);
    }

    function update_check($checked) {
        $this->is_check = $checked;
        global $oUser;
        $params = [$checked ? 1 : 0, $checked ? $oUser->id : NULL, $this->check_note, $this->id];
        $sql = "UPDATE visit SET is_check = ?, check_author = ?, check_ludati = " . ($checked ? "NOW()" : "NULL") . ",
            check_note = ?
            WHERE id_visita = ? ";
        Database::edit($sql, $params);
    }

    function delete($note = '') {
        global $oUser;
        if (isset($this->id) && $this->id != 0 && isset($oUser) && $oUser->id != 0) {
            $sql = "INSERT INTO visit_deleted (id_visita, note, visit_json, author, ludati) VALUES (?, ?, ?, ?, NOW());
                DELETE FROM visit WHERE id_visita = ?; ";
            $params = [$this->id, $note, json_encode($this), $oUser->id, $this->id];
            Database::edit($sql, $params);
        }
    }

    //-----------------------------------------------------STATIC
    static function get_all_by_id_paz($id_paz) {
        $params = [$id_paz];
        $sql = "SELECT VV.* FROM " . self::get_default_select() . " VV 
            WHERE VV.id_paz = ? ORDER BY VV.date_visit DESC, id_visita ";
        $visits = Database::read($sql, $params);

        // the code below will work correctly only with this specific sorting (date_visit, id_visita)
        // another approach is used in Visit_table class

        $oVisits = [];
        foreach ($visits as $row) {
            $oVisit = new Visit();
            $oVisit->set_by_row($row);
            $oVisits[] = $oVisit;
        }
        return $oVisits;
    }

    public static function get_by_author($author_id) {
        $sql = "SELECT * FROM " . self::get_default_select() . " WHERE author = ?";
        return Database::read($sql, [$author_id]);
    }

    function get_last($id_paz, $only_programmed = false) {
        $params = [$id_paz];
        $sql = " SELECT VV.* FROM " . self::get_default_select() . " VV
            WHERE VV.id_visita = (SELECT VV.id_visita FROM visit VV
                WHERE VV.id_paz = ?
                " . ($only_programmed ? "AND VV.is_extra = 0" : "") . "
                ORDER BY VV.date_visit DESC LIMIT 1) ";
        $visits = Database::read($sql, $params);
        $this->get_oVisit($visits);
    }

    function get_previous($id_paz, $date_visit, $only_programmed = false) {
        $params = [$id_paz, $date_visit];
        $sql = " SELECT VV.* FROM " . self::get_default_select() . " VV
            WHERE VV.id_visita = (SELECT VV.id_visita FROM visit VV
                WHERE VV.id_paz = ? AND VV.date_visit < ?
                " . ($only_programmed ? "AND VV.is_extra = 0" : "") . "
                ORDER BY VV.date_visit DESC LIMIT 1) ";
        $visits = Database::read($sql, $params);
        $this->get_oVisit($visits);
    }

    function get_next($id_paz, $oVisit, $only_programmed = false) {
        $params = [$id_paz, $oVisit->date, $oVisit->id];
        $sql = " SELECT VV.* FROM " . self::get_default_select() . " VV
            WHERE VV.id_visita = (SELECT VV.id_visita FROM visit VV
                WHERE VV.id_paz = ? AND VV.date_visit > ? AND VV.id_visita <> ?
                " . ($only_programmed ? "AND VV.is_extra = 0" : "") . "
            ORDER BY VV.date_visit ASC LIMIT 1) ";
        $visits = Database::read($sql, $params);
        $this->get_oVisit($visits);
    }

    //-----------------------------------------------------PUBLIC

    function can_be_locked() {
        $sql = "SELECT V.id_paz, V.id_visita, V.date_visit FROM visit V
            WHERE V.id_paz = ? AND V.date_visit < ? AND V.is_lock = 0";
        $params = [$this->id_paz, $this->date];
        $rows = Database::read($sql, $params);
        return count($rows) == 0;
    }

    function can_be_unlocked() {
        $sql = "SELECT V.id_paz, V.id_visita, V.date_visit FROM visit V
            WHERE V.id_paz = ? AND V.date_visit > ? AND V.is_lock = 1";
        $params = [$this->id_paz, $this->date];
        $rows = Database::read($sql, $params);
        return count($rows) == 0;
    }

    public function check_status($level) {
        switch ($level) {
            case self::CONFIRM_TYPE_VLOCK:
                if (!$this->is_lock) {
                    return false;
                }
                break;
            case self::CONFIRM_TYPECHECK:
                if (!$this->is_check) {
                    return false;
                }
                break;
            default:
                throw new InvalidArgumentException();
        }

        return true;
    }

    public function has_forms_started() {
        $forms_started = Database::read("SELECT * FROM form_status WHERE id_visita = ? ", [$this->id]);
        return count($forms_started) > 0;
    }

    //-----------------------------------------------------PRIVATE

    static function get_all_removed() {
        return Database::read("SELECT id_visita AS id, visit_json, note, author, ludati FROM visit_deleted WHERE id_visita IS NOT NULL ORDER BY ludati DESC", []);
    }

    static function get_default_select() {
        return "( SELECT DISTINCT P.id_paz, P.id_center, P.id_end_reason, P.date_end,
            V.id_visita, V.date_visit, V.author AS visit_author, V.ludati AS visit_ludati, V.is_lock, V.lock_author, V.lock_ludati, V.lock_reason,
            V.is_check, V.check_author, V.check_ludati, V.check_note, V.unlock_reason, V.unlock_note, V.unlock_author, V.unlock_ludati,
            V.visit_type_id, VT.visit_type_code, VT.visit_type, VT.visit_day, VT.always_show, VT.is_extra, VT.visit_day_lower, VT.visit_day_upper,
            VT.has_output, VT.has_randomization
            FROM patient P
                INNER JOIN visit V ON P.id_paz = V.id_paz
                INNER JOIN visit_type VT ON VT.visit_type_id = V.visit_type_id
            )";
    }

    private function get_oVisit($visits) {
        if (count($visits) > 0) {
            $this->set_by_row($visits[0]);
        }
    }

}
