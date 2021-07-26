<?php

//Baseline visit should be 1, visit_day set to 0 is used for special visit like unscheduled, safety
//is_extra = 0 are the scheduled ones
//always_shows = 1 are the one always displayed in the suggested visits
class Visit_type {

    public $id = 0;
    public $name = '';
    public $code = '';
    public $day = null;
    public $day_lower = null;
    public $day_upper = null;
    public $always_show = false;
    public $is_extra = false;
    public $has_output = false;
    public $has_randomization = false;
    public static $visit_list_mode = true;
    public $forms = [];

    //-----------------------------------------------------CONSTRUCT & INTERFACE METHODS
    public function __construct($visit_type = []) {
        if (count($visit_type) > 0) {
            $this->set_by_row($visit_type);
        }
    }

    function get_by_id($visit_type_id) {
        $this->id = $visit_type_id;
        $visit_types = Database::read("SELECT VT.*
            FROM visit_type VT
            WHERE VT.visit_type_id = ? ", [$this->id]);
        if (count($visit_types) > 0) {
            $this->set_by_row($visit_types[0]);
        }
    }
    public function get_name() {
        return Language::find($this->name, ['visit']);
    }

    public function set_by_row($visit_type) {
        $this->id = $visit_type["visit_type_id"];
        $this->name = $visit_type["visit_type"];
        $this->code = $visit_type["visit_type_code"];
        $this->day = $visit_type["visit_day"];
        $this->day_lower = (!isset($visit_type["visit_day_lower"]) ? null : $visit_type["visit_day_lower"]);
        $this->day_upper = (!isset($visit_type["visit_day_upper"]) ? null : $visit_type["visit_day_upper"]);
        $this->always_show = $visit_type["always_show"] . '' == '1';
        $this->is_extra = $visit_type["is_extra"] . '' == '1';
        $this->has_output = $visit_type["has_output"] . '' == '1';
        $this->has_randomization = $visit_type["has_randomization"] . '' == '1';
        if (!self::$visit_list_mode)
            $this->get_forms();
    }

    static function get_all($id_paz, $id_visita) {
        $params = [$id_paz];
        if ($id_visita . '' != '0') {
            $params[] = $id_visita;
        }
        $sql = "SELECT VT.*
            FROM visit_type VT
            WHERE (VT.always_show = 1 OR VT.visit_day > IFNULL(
                (
                    SELECT VV.visit_day
                    FROM ". Visit::get_default_select() ." VV 
                    WHERE VV.id_paz=? ".($id_visita . '' == '0' ? "  " : " AND VV.date_visit < (SELECT date_visit FROM visit WHERE id_visita = ? LIMIT 1) ").
                    " ORDER BY VV.date_visit DESC LIMIT 1
                ), -1))
            ORDER BY VT.always_show, VT.visit_day, VT.visit_type ";
        $visit_types = Database::read($sql, $params);
        $oVisit_types = [];
        foreach ($visit_types as $visit_type) {
            $oVisit_type = new Visit_type($visit_type);
            $oVisit_types[] = $oVisit_type;
        }
        return $oVisit_types;
    }

    //-----------------------------------------------------PUBLIC
    public function get_forms() {
        $forms = Database::read("SELECT * FROM form_visit_type WHERE visit_type_id = ? ORDER BY order_id ", [$this->id]);
        $this->forms = [];
        foreach ($forms as $form) {
            $this->forms[] = $form['form_id'];
        }
    }

    function create() {
        $params = [$this->name, $this->code, $this->day, $this->day_lower, $this->day_upper, $this->always_show, $this->is_extra, $this->has_output, $this->has_randomization];
        $sql = "INSERT INTO visit_type (visit_type, visit_type_code, visit_day, visit_day_lower, visit_day_upper, always_show, is_extra, has_output, has_randomization) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $this->id = Database::edit($sql, $params, true);
    }

    function update() {
        $params = [$this->name, $this->code, $this->day, $this->day_lower, $this->day_upper, $this->always_show, $this->is_extra, $this->has_output, $this->has_randomization, $this->id];
        $sql = "UPDATE visit_type SET visit_type = ?, visit_type_code = ?, visit_day = ?, visit_day_lower = ?, visit_day_upper = ?, 
            always_show = ?, is_extra = ?, has_output = ?, has_randomization = ?
            WHERE visit_type_id = ? ";
        Database::edit($sql, $params);
    }

    function delete() {
        $params = [$this->id];
        $sql = "DELETE FROM visit_type WHERE visit_type_id = ? ";
        Database::edit($sql, $params);
    }

    static function suggests($days, &$oVisit_types) {
        $oTypes_admitted = [];
        $oSuggested = NULL;
        foreach($oVisit_types as $oType) {
            if ($days >= $oType->day_lower && $days <= $oType->day_upper) {
                $oSuggested = $oType;
                $oTypes_admitted[] = $oType;
            }
            if ($oType->always_show) {
                $oTypes_admitted[] = $oType;
            }
        }
        $oVisit_types = $oTypes_admitted;
        return $oSuggested;
    }
}
