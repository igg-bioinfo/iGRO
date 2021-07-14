<?php

class Diagnosis {

    public $id = 0;
    public $name = '';
    public $name_short = '';
    public $is_provisional = NULL;
    public $group_name = '';
    public $orderby = 0;

    //-----------------------------------------------------CONSTRUCT
    function __construct($diagnosis = []) {
        if (count($diagnosis) > 0) {
            $this->set_by_row($diagnosis);
        }
    }

    public function get_by_id($id) {
        $diagnosis_list = Database::read("SELECT D.* FROM diagnosis D WHERE D.id_dia = ? ", [$id]);
        if (count($diagnosis_list) > 0) {
            $this->set_by_row($diagnosis_list[0]);
        }
    }

    public function set_by_row($diagnosis) {
        $this->id = $diagnosis["id_dia"];
        $this->name = $diagnosis["dia_name"];
        $this->name_short = $diagnosis["dia_short"];
        $this->group_name = $diagnosis["group_name"];
        $this->orderby = $diagnosis["orderby"];
    }

    //-----------------------------------------------------STATIC
    public static function get_all($group_name = '') {
        $sql = "SELECT D.* FROM diagnosis D ";
        $params = [];
        if ($group_name != '') {
            $sql .= " WHERE DL.group_name = ? ";
            $params[] = $group_name;
        }
        $diagnosis_list = Database::read($sql, $params);
        $oDiagnosis_list = [];
        foreach ($diagnosis_list as $diagnosis) {
            $oDiagnosis = new Diagnosis($diagnosis);
            $oDiagnosis_list[] = $oDiagnosis;
        }
        return $oDiagnosis_list;
    }

    //--------------------------------------SAVE
    public function save() {
        if ($this->id == 0) {
            $this->insert();
        } else {
            $this->update();
        }
    }

    private function insert() {
        $sql = "INSERT INTO diagnosis (dia_name, dia_short, group_name, orderby)
            SELECT ?, ?, ?, ?; ";
        $params = [$this->name, $this->name_short, $this->group_name, $this->orderby];
        $this->id = Database::edit($sql, $params, true);
    }

    private function update() {
        $sql = "UPDATE diagnosis
            SET dia_name = ?, dia_short = ?, group_name = ?, orderby = ?
            WHERE id_dia = ? ";
        $params = [$this->name, $this->name_short, $this->group_name, $this->orderby, $this->id];
        Database::edit($sql, $params);
    }
}
