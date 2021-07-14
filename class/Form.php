<?php

class Form {

    public $id = 0;
    public $type = '';
    public $class = '';
    public $title = '';
    public $oFields = [];
    public $oValues = [];
    public $group = '';
    public $is_required = false;
    public $is_completed = false;
    public $skip_controls = false;
    private $dependencies = '';
    public $oParents = [];
    public $author = NULL;
    public $ludati = NULL;
    public $page_current = 0;
    public $page_last = 0;
    public $is_visit_related = NULL;
    public $main_table = '';
    public $main_field = '';
    public $main_value = NULL;
    public $other_main_field = '';
    public $other_main_value = NULL;
    public $other_fields_tables = [];
    private $tables = [];
    public $sql = "";

    //-----------------------------------------------------CONSTRUCT
    function __construct($form_type = '', $main_value = NULL, $page_current = 0) {
        $this->main_value = $main_value;
        $this->page_current = $page_current;
        if ($form_type . '' != '') {
            $params = [$form_type];
            $sql = "SELECT FM.*
                FROM (" . Database::get_view_field_mapper() . ") FM
                WHERE FM.form_type = ?
                ORDER BY FM.page_number, FM.order_id";
            $form_fields = Database::read($sql, $params);
            if (count($form_fields) == 0) {
                return;
            }
            $this->set_by_row($form_fields[0]);
            foreach ($form_fields as $field) {
                $oField = new Field($field);
                if ($this->page_current == 0 || $this->page_current == $oField->page_number) {
                    $this->oFields[$oField->name] = $oField;
                }
                $this->page_last = $oField->page_number;
            }
            $this->add_key_field_for_all($this->main_field, $this->main_value);
        }
    }

    private function array_remove_object(&$array, $value, $prop) {
        return array_filter($array, function($a) use($value, $prop) {
            return $a->$prop !== $value;
        });
    }

    public function remove_fields($field_names) {
        foreach ($field_names as $field_name) {
            $this->oFields = $this->array_remove_object($this->oFields, $field_name, 'name');
        }
    }

    public function get_by_id($form_id, $page_current = 0, $by_field = true) {
        $this->page_current = $page_current;
        $params = [$form_id];
        $sql = "SELECT FM.*
            FROM (" . ($by_field ? Database::get_view_field_mapper() : Database::get_view_form_mapper()) . ") FM
            WHERE FM.form_id = ? 
            ORDER BY FM.page_number, FM.order_id";
        $form_fields = Database::read($sql, $params);
        if (count($form_fields) == 0) {
            return;
        }
        $this->set_by_row($form_fields[0]);
        foreach ($form_fields as $field) {
            $oField = new Field($field);
            if ($field['field_name'].'' != '' && ($this->page_current == 0 || $this->page_current == $oField->page_number)) {
                $this->oFields[$oField->name] = $oField;
            }
            $this->page_last = $oField->page_number;
        }
    }

    public function set_main_value($main_value) {
        $this->main_value = $main_value;
        $this->add_key_field_for_all($this->main_field, $this->main_value);
    }

    public function set_by_row($form) {
        $this->id = $form['form_id'];
        $this->type = $form['form_type'];
        $this->class = $form['form_class'];
        $this->title = $form['form_title'];
        $this->is_completed = isset($form['is_completed']) && $form['is_completed'] . '' == '1';
        $this->author = isset($form['author']) ? $form['author'] : NULL;
        $this->ludati = isset($form['ludati']) ? $form['ludati'] : NULL;
        if (isset($form['page'])) {
            $this->page_current = $form['page'];
        }
        if (isset($form['is_required'])) {
            $this->is_required = $form['is_required'] . '' == '1';
        }
        if (isset($form['group_name'])) {
            $this->group = $form['group_name'];
        }
        if (isset($form['dependencies'])) {
            $this->dependencies = $form['dependencies'];
        }

        $this->is_visit_related = $form['is_visit_related'] . '' == '1';
        $this->main_table = $this->is_visit_related ? 'visit' : 'patient';
        $this->main_field = $this->is_visit_related ? 'id_visita' : 'id_paz';
    }

    function get_title(){
        return $this->title.'' != '' ? Language::find($this->title, [$this->class]) : '';
    }

    //-----------------------------------------------------STATIC
    public function get_all_dependencies($visit_type_id, $id_paz, $id_visita) {
        $this->oParents = [];
        if ($this->dependencies == '') {
            return;
        }
        $params = [];
        $params[] = $id_paz;
        $params[] = $id_visita;
        $params[] = $visit_type_id;
        $forms = Database::read("SELECT DISTINCT FM.form_id, FM.form_type, FM.form_class, FM.form_title, FM.is_visit_related, FVT.*,
            IFNULL(FSV.is_completed, FSP.is_completed) AS is_completed,
            IFNULL(FSV.page, FSP.page) AS page,
            IFNULL(FSV.author, FSP.author) AS author,
            IFNULL(FSV.ludati, FSP.ludati) AS ludati
            FROM form_visit_type FVT
            INNER JOIN form FM ON FVT.form_id = FM.form_id
            LEFT OUTER JOIN form_status FSP ON FM.is_visit_related IS NULL AND FSP.form_id = FM.form_id AND FSP.id_paz = ?
            LEFT OUTER JOIN form_status FSV ON FM.is_visit_related = 1 AND FSV.form_id = FM.form_id AND FSV.id_visita = ?
            WHERE FVT.visit_type_id = ? AND FM.form_id IN (" . $this->dependencies . ")
            ORDER BY FVT.is_required DESC, FVT.order_id ", $params);
        if (count($forms) == 0) {
            return;
        }
        foreach ($forms as $form) {
            $oForm = new Form();
            $oForm->set_by_row($form);
            $this->oParents[] = $oForm;
        }
    }

    static function get_all_by_visit_type($visit_type_id, $id_paz, $id_visita) {
        $oForms = [];
        $params = [];
        $params[] = $id_paz;
        $params[] = $id_visita;
        $params[] = $visit_type_id;
        $forms = Database::read("SELECT DISTINCT FM.form_id, FM.form_type, FM.form_class, FM.form_title, FM.is_visit_related, FVT.*,
            IFNULL(FSV.is_completed, FSP.is_completed) AS is_completed,
            IFNULL(FSV.page, FSP.page) AS page,
            IFNULL(FSV.author, FSP.author) AS author,
            IFNULL(FSV.ludati, FSP.ludati) AS ludati
            FROM form_visit_type FVT
            INNER JOIN form FM ON FVT.form_id = FM.form_id
            LEFT OUTER JOIN form_status FSP ON FM.is_visit_related IS NULL AND FSP.form_id = FM.form_id AND FSP.id_paz = ?
            LEFT OUTER JOIN form_status FSV ON FM.is_visit_related = 1 AND FSV.form_id = FM.form_id AND FSV.id_visita = ?
            WHERE FVT.visit_type_id = ?
            ORDER BY FVT.order_id, FVT.is_required DESC ", $params);
        foreach ($forms as $form) {
            $oForm = new Form();
            $oForm->set_by_row($form);
            $oForms[] = $oForm;
        }
        return $oForms;
    }

    static function get_forms_with_common_fields($form_id, $oFields) {
        $oForms = [];
        $params = [];
        $params[] = $form_id;
        $ids = '';
        foreach ($oFields as $oField) {
            $ids .= $oField->id . ', ';
        }
        $ids .= '0';
        $form_fields = Database::read("SELECT FM.*
            FROM (" . Database::get_view_field_mapper() . ") FM
            WHERE FM.form_id <> ? AND FM.form_id IN (SELECT DISTINCT form_id FROM field_form WHERE field_id IN (" . $ids . "))
            ORDER BY FM.form_id, FM.page_number, FM.order_id ", $params);
        if (count($form_fields) == 0) {
            return $oForms;
        }
        $id_old = 0;
        $oForm = null;
        foreach ($form_fields as $form) {
            if ($id_old != $form['form_id']) {
                if ($id_old != 0) {
                    $oForms[] = $oForm;
                }
                $oForm = new Form();
                $oForm->set_by_row($form);
                $id_old = $form['form_id'];
            }
            $oField = new Field($form);
            $oForm->oFields[] = $oField;
        }
        if ($id_old != 0) {
            $oForms[] = $oForm;
        }
        return $oForms;
    }

    //-----------------------------------------------------ADD KEY & OTHER FIELD
    function add_other_field($table, $field_name, $value) {
        $this->add_other_field_value(false, $table, $field_name, $value);
    }

    function add_key_field($table, $field_name, $value, $is_autoincremental = false) {
        $this->add_other_field_value(true, $table, $field_name, $value, $is_autoincremental);
    }

    function add_key_field_for_all($field_name, $value, $is_autoincremental = false) {
        $tables = [];
        if ($is_autoincremental) {
            $this->other_main_field = $field_name;
            $this->other_main_value = $value;
        }
        foreach ($this->oFields as $oField) {
            if (!in_array($oField->table_name, $tables)) {
                $this->add_other_field_value(true, $oField->table_name, $field_name, $value, $is_autoincremental);
                $tables[] = $oField->table_name;
            }
        }
    }

    private function add_other_field_value($is_key, $table, $field_name, $value, $is_autoincremental = false) {
        $table_found = false;
        $table = strtolower($table);
        for ($t = 0; $t < count($this->other_fields_tables); $t++) {
            if (strcasecmp($table, $this->other_fields_tables[$t]['table_name']) == 0) {
                $other_field_found = false;
                for ($f = 0; $f < count($this->other_fields_tables[$t]['other_fields']); $f++) {
                    if (strcasecmp($this->other_fields_tables[$t]['other_fields'][$f]['field_name'], $field_name) == 0) {
                        $this->other_fields_tables[$t]['other_fields'][$f]['value'] = $value;
                        $this->other_fields_tables[$t]['other_fields'][$f]['is_key'] = $is_key;
                        $this->other_fields_tables[$t]['other_fields'][$f]['is_auto'] = $is_autoincremental;
                        $other_field_found = true;
                        break;
                    }
                }
                if (!$other_field_found) {
                    array_push($this->other_fields_tables[$t]['other_fields'], array('field_name' => $field_name, 'value' => $value, 'is_key' => $is_key, 'is_auto' => $is_autoincremental));
                }
                $table_found = true;
            }
        }
        if (!$table_found) {
            $this->other_fields_tables[] = ['table_name' => $table, 'other_fields' => [array('field_name' => $field_name, 'value' => $value, 'is_key' => $is_key, 'is_auto' => $is_autoincremental)]];
        }
    }

    //-----------------------------------------------------GET
    public function get_values($order = '') {
        $this->sql = "";
        $this->tables = [];
        $this->get_tables_joins();

        if (count($this->tables) == 0) {
            return [];
        }
        $sql = "SELECT ";
        foreach ($this->other_fields_tables as $other_fields_table) {
            foreach ($other_fields_table['other_fields'] as $other_field) {
                $field = $this->tables[0] . "." . $other_field['field_name'] . ", ";
                if (!Strings::contains($sql, $field)) {
                    $sql .= $field;
                }
            }
        }
        $fields = [];
        $has_extra = false;
        foreach ($this->oFields as $oField) {
            foreach ($this->tables as $table) {
                $field_table = 't_' . strtolower($oField->table_name);
                if (strcasecmp($field_table, $table) == 0) {
                    if ($oField->is_extra_field) {
                        if (!$has_extra) { $fields[] = 'extra_fields'; }
                        $has_extra = true;
                        break;
                    }
                    $fields[] = $oField->name;
                    break;
                }
            }
        }
        $sql .= join(', ', $fields);
        $sql .= " FROM " . $this->main_table . " MAIN_TABLE ";
        $this->sql = $sql . $this->sql . " WHERE MAIN_TABLE." . $this->main_field . " = " . $this->main_value;
        if ($order != '') {
            $this->sql .= " ORDER BY " . $order;
        }

        $values = Database::read($this->sql, []);
        $this->oValues = [];
        for ($v = 0; $v < count($values); $v++) {
            $oRow = [];
            $oExtras = isset($values[$v]['extra_fields']) ? json_decode($values[$v]['extra_fields']) : false;
            foreach ($this->oFields as $oField) {
                $oField = clone $this->oFields[$oField->name];
                if ($oField->is_extra_field && $oExtras){
                    $oField->value = isset($oExtras->{$oField->name}) ? $oExtras->{$oField->name} : NULL;
                } else if (isset($values[$v][$oField->name])) {
                    $oField->value = $values[$v][$oField->name];
                }
                $oRow[$oField->name] = $oField;
            }

            foreach ($this->other_fields_tables as $other_fields_table) {
                foreach ($other_fields_table['other_fields'] as $other_field) {
                    if (isset($values[$v][$other_field['field_name']])) {
                        $oField = new Field();
                        $oField->name = $other_field['field_name'];
                        $oField->value = $values[$v][$other_field['field_name']];
                        $oRow[$oField->name] = $oField;
                    }
                }
            }

            if ($this->other_main_field . '' != '') {
                if (isset($values[$v][$this->other_main_field])) {
                    $this->oValues[$values[$v][$this->other_main_field]] = $oRow;
                } else {
                    URL::redirect('error', 7783);
                }
            } else {
                if (count($values) > 1) {
                    URL::redirect('error', 7784);
                } else {
                    $this->oValues[0] = $oRow;
                }
            }
        }
        $this->get_author_values();
    }

    public function get_valued_field($field_name) {
        $oField = false;
        if (!isset($this->other_main_value) || $this->other_main_value == 0) {
            if (isset($this->oValues[0][$field_name])) {
                $oField = $this->oValues[0][$field_name];
            } else if (isset($this->oFields[$field_name])) {
                $oField = $this->oFields[$field_name];
            }
        } else if (isset($this->oValues[$this->other_main_value])) {
            if (isset($this->oValues[$this->other_main_value])) {
                $oRow = $this->oValues[$this->other_main_value];
                if (array_key_exists($field_name, $oRow)) {
                    $oField = $oRow[$field_name];
                }
            } else if (isset($this->oFields[$field_name])) {
                $oField = $this->oFields[$field_name];
            }
        }
        return $oField;
    }

    //-----------------------------------------------------PRIVATE GET
    private function get_tables_joins() {
        if (count($this->oFields) == 0) {
            return;
        }
        foreach ($this->other_fields_tables as $other_fields_table) {
            $sql_get_fields = [];
            $sql_get_where = '';
            foreach ($other_fields_table['other_fields'] as $other_field) {
                $sql_get_fields[] = $other_field['field_name'];
                if ($other_field['is_key'] && (!$other_field['is_auto'] || $other_field['value'] . '' != '0')) {
                    $sql_get_where .= $other_field['field_name'] . " = '" . $other_field['value'] . "' AND ";
                }
            }
            $has_extra = false;
            foreach ($this->oFields as $oField) {
                if (strcasecmp($other_fields_table['table_name'], $oField->table_name) == 0) {
                    if ($oField->is_extra_field) {
                        //$sql_get_fields[] = "JSON_VALUE([extra_fields],'$." . $oField->name . "') AS " . $oField->name;
                        if (!$has_extra) { $sql_get_fields[] = 'extra_fields'; }
                        $has_extra = true;
                    } else {
                        $sql_get_fields[] = $oField->name;
                    }
                }
            }
            $table_sql = " ( SELECT " . join(', ', $sql_get_fields) . " FROM " . $other_fields_table['table_name'] . " WHERE " . rtrim($sql_get_where, "AND ") . " ) ";
            $table_name = "t_" . $other_fields_table['table_name'];
            $this->set_join($table_sql, $table_name);
            $this->tables[] = $table_name;
        }
    }

    private function set_join($table_sql, $table_name) {
        $this->sql .= " INNER JOIN " . $table_sql . " " . $table_name . " ON ";
        $this->sql .= " " . $table_name . "." . $this->main_field . " = MAIN_TABLE." . $this->main_field . " AND ";
        if (count($this->tables) - 1 >= 0) {
            foreach ($this->other_fields_tables as $other_table) {
                if (strcasecmp($other_table['table_name'], $table_name) != 0) {
                    continue;
                }
                foreach ($other_table['other_fields'] as $other_field) {
                    if ($other_field['is_key']) {
                        $this->sql .= " " . $table_name . "." . $other_field['field_name'] . " = " . $this->tables[count($this->tables) - 1] . "." . $other_field['field_name'] . " AND ";
                    }
                }
            }
        }
        $this->sql = rtrim($this->sql, "AND ");
    }

    private function get_author_values() {
        if (count($this->oValues) == 1) {
            $author = [];
            $this->author = NULL;
            $this->ludati = NULL;
            $this->is_completed = false;
            if (!isset($this->other_main_value)) {
                $params = [$this->id, $this->main_value];
                $sql = "SELECT FS.is_completed, FS.author, FS.ludati FROM form_status FS WHERE FS.form_id = ? AND FS." . $this->main_field . " = ? ";
                $author = Database::read($sql, $params);
            } else if (isset($this->other_main_value) && $this->other_main_value != 0) {
                $params = [$this->other_main_value];
                $sql = "SELECT 0 AS is_completed, author, ludati FROM " . $this->other_fields_tables[0]['table_name'] . " FS WHERE FS." . $this->other_main_field . " = ? ";
                $author = Database::read($sql, $params);
            }

            if (count($author) == 1) {
                $this->author = $author[0]['author'];
                $this->ludati = $author[0]['ludati'];
                $this->is_completed = $author[0]['is_completed'] == 1;
            }
        }
    }

    //-----------------------------------------------------SAVE
    public function save() {
        global $oUser;
        if (count($this->oFields) == 0) {
            return;
        }
        foreach ($this->other_fields_tables as $other_fields_table) {
            $sql_update = [];
            $params_update = [];

            $sql_insert_fields = [];
            $sql_insert_values = [];
            $params_insert = [];

            //Extra fields
            $extra_fields_update = '';
            $extra_fields_update = new stdClass();
            $extra_fields_insert = new stdClass();

            $found_one_field = false;
            foreach ($this->oFields as $oField) {
                if (strcasecmp($other_fields_table['table_name'], $oField->table_name) == 0) {
                    $post_value = $this->validate_field($oField);
                    if ($oField->is_extra_field) {
                        $extra_fields_update->{$oField->name} = $post_value;
                        if (!is_null($post_value)) {
                            $extra_fields_insert->{$oField->name} = $post_value;
                        }
                    } else {
                        $sql_update[] = $oField->name . " = ?";
                        $params_update[] = $post_value;

                        $sql_insert_fields[] = $oField->name;
                        $sql_insert_values[] .= "?";
                        $params_insert[] = $post_value;
                    }
                    $found_one_field = true;
                }
            }
            //Extra fields
            if (count((array)$extra_fields_update) != 0) {
                $sql_update[] = "extra_fields = '" . json_encode($extra_fields_update)."'";

                $sql_insert_fields[] = "extra_fields";
                $sql_insert_values[] = "?";
                $params_insert[] = json_encode($extra_fields_insert);
            }
            if (!$found_one_field) {
                continue;
            }

            foreach ($other_fields_table['other_fields'] as $other_field) {
                if (!$other_field['is_key']) {
                    $sql_update[] = $other_field['field_name'] . ' = ?';
                    $params_update[] = $other_field['value'];

                    $sql_insert_fields[] = $other_field['field_name'];
                    $sql_insert_values[] = '?';
                    $params_insert[] = $other_field['value'];
                } else if (!$other_field['is_auto']) {
                    $sql_insert_fields[] = $other_field['field_name'];
                    $sql_insert_values[] = '?';
                    $params_insert[] = $other_field['value'];
                }
            }

            //AUTHOR LUDATI
            $sql_update[] = ' author = ?';
            $params_update[] = $oUser->id;
            $sql_update[] = ' ludati = NOW()';

            $sql_insert_fields[] = 'author';
            $sql_insert_values[] = '?';
            $params_insert[] = $oUser->id;
            $sql_insert_fields[] = 'ludati';
            $sql_insert_values[] = 'NOW()';

            $sql_update_string = join(', ', $sql_update) . " WHERE ";

            $sql_update_where = ' WHERE ';
            $params_update_where = [];
            foreach ($other_fields_table['other_fields'] as $other_field) {
                if ($other_field['is_key']) {
                    $sql_update_string .= $other_field['field_name'] . " = ? AND ";
                    $sql_update_where .= $other_field['field_name'] . " = ? AND ";
                    $params_update[] = $other_field['value'];
                    $params_update_where[] = $other_field['value'];
                }
            }
            $this->insert_or_update($other_fields_table['table_name'], 
                $sql_update_string, $sql_update_where, $sql_insert_fields, $sql_insert_values, 
                $params_update, $params_update_where, $params_insert);
        }
    }

    public function check_form_status() {
        $is_completed = NULL;
        $sql = "SELECT is_completed FROM form_status WHERE form_id = ? AND " . $this->main_field . " = ? ";
        $params = [$this->id, $this->main_value];
        $rows = Database::read($sql, $params);
        if (count($rows)) {
            $is_completed = $rows[0]['is_completed'];
        }
        return $is_completed;
    }

    public function save_form_status() {
        global $oUser;
        $sql_update = "is_completed = ?, page = ?, author = ?, ludati = NOW() ";
        $sql_update_where = " WHERE form_id = ? AND " . $this->main_field . " = ? ";
        $sql_insert_fields = ["is_completed", "page", "author", "ludati", "form_id", $this->main_field];
        $sql_insert_values = ["?", "?", "?", "NOW()", "?", "?"];
        $table = "form_status";
        $params_update = [$this->is_completed, $this->page_current, $oUser->id];
        $params_update_where = [$this->id, $this->main_value];
        $params_insert = [$this->is_completed, $this->page_current, $oUser->id, $this->id, $this->main_value];
        $this->insert_or_update($table, 
            $sql_update, $sql_update_where, $sql_insert_fields, $sql_insert_values, 
            $params_update, $params_update_where, $params_insert);
        $this->update_other_form_status_involved();
    }

    public function delete_form_status() {
        global $oUser;
        $sql = "DELETE FROM form_status WHERE form_id = ? AND " . $this->main_field . " = ? ";
        $params = [$this->id, $this->main_value];
        Database::edit($sql, $params);
    }

    //-----------------------------------------------------PRIVATE SAVE
    private function update_other_form_status_involved() {
        global $oUser;
        $ids = '';
        foreach ($this->oFields as $oField) {
            $ids .= $oField->id . ', ';
        }
        $form_fields = Database::read("SELECT DISTINCT form_id FROM field_form WHERE form_id != ? AND field_id IN (" . $ids . "0)", [$this->id]);
        if (count($form_fields) > 0) {
            $sql = "UPDATE form_status SET author = ?, ludati = NOW() WHERE " . $this->main_field . " = ? AND form_id IN (";
            foreach ($form_fields as $form) {
                $sql .= $form["form_id"] . ", ";
            }
            $sql .= "0)";
            Database::edit($sql, [$oUser->id, $this->main_value]);
        }
    }

    private function insert_or_update($table, $sql_update, $sql_update_where, $sql_insert_fields, $sql_insert_values, $params_update, $params_update_where, $params_insert) {
        $sql = "SELECT * FROM " . $table . ' '. rtrim($sql_update_where, "AND ");
        $found = Database::read($sql, $params_update_where);
        if (count($found) == 0) {
            $sql = "INSERT INTO " . $table . " (" . join(', ', $sql_insert_fields) . ") VALUES (" . join(', ', $sql_insert_values) . ");";
            Database::edit($sql, $params_insert);
        } else {
            $sql = "UPDATE " . $table . " SET " . rtrim($sql_update, "AND ").";";
            Database::edit($sql, $params_update);
        }
    }

    //-----------------------------------------------------VALIDATE
    public function validate_field($oField) {
        $post_value = Security::sanitize(INPUT_POST, $oField->name, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
        if (is_array($post_value)) {
            $values = array_map(function($value) {
                return json_decode(html_entity_decode($value));
            }, $post_value);

            if (json_last_error() != JSON_ERROR_NONE) { // not valid json
                $post_value = json_encode($post_value);
            } else {
                $post_value = json_encode($values);
            }
        } else {
            $post_value = Security::sanitize(INPUT_POST, $oField->name, FILTER_SANITIZE_SPECIAL_CHARS);
        }
        $has_error = false;

        // fix for saving float or integer or string as NULL instead of zero/empty value
        if ($post_value == '') {
            switch ($oField->type) {
                case Field::TYPE_BOOL_0:
                    // all skipped fields should always be saved as null
                    $post_value = $this->skip_controls ? NULL : 0;
                    break;
                case Field::TYPE_STRING:
                case Field::TYPE_FLOAT:
                case Field::TYPE_INT:
                case Field::TYPE_BOOL:
                case Field::TYPE_BOOL_99:
                    $post_value = NULL;
                    break;
            }
        }

        // if skip_controls is not used (default value is false), do normal validation of required fields
        if (($oField->required == 1 || $oField->force_required) && $post_value . '' == '' && $this->skip_controls === false) {
            $has_error = true;
        }
        if (($oField->type == Field::TYPE_BOOL || $oField->type == Field::TYPE_BOOL_0) && $post_value != '') {
            if (!is_numeric($post_value) || ($post_value . '' != '0' && $post_value . '' != '1')) {
                $has_error = true;
            }
        } else if ($oField->type == Field::TYPE_BOOL_99 && $post_value != '') {
            if (!is_numeric($post_value) || ($post_value . '' != '0' && $post_value . '' != '1' && $post_value . '' != '99')) {
                $has_error = true;
            }
        } else if ($oField->type == Field::TYPE_INT && $post_value != '') {
            if (!ctype_digit(strval($post_value))) {
                $has_error = true;
            }
        } else if ($oField->type == Field::TYPE_STRING && $post_value != '') {
            $string_length = strlen($post_value);
            if ($oField->limit_min > 0 && $string_length < $oField->limit_min) {
                $has_error = true;
            }
            if ($oField->limit_max > 0 && $string_length > $oField->limit_max) {
                $has_error = true;
            }
        } else if ($oField->type == Field::TYPE_DATE) {
            if ($post_value . '' == '') {
                $post_value = NULL;
                // Check if input date can be used to create datetime object
            } else if (Date::screen_to_object($post_value) === false) {
                $has_error = true;
            } else {
                $post_value = Date::screen_to_default($post_value);
            }
        } else if ($oField->type == Field::TYPE_FLOAT && $post_value != '') {
            if (!is_numeric($post_value)) {
                $has_error = true;
            }
        }
        if ($has_error) {
            URL::redirect('error', 7777);
        }

        // convert all extra field values to string to maintain proper quoting inside JSON strings
        if ($oField->is_extra_field && !is_null($post_value)) {
            $post_value = (string) $post_value;
        }

        return $post_value;
    }

    //-----------------------------------------------------DELETE
    public function delete() {
        $params_delete = [];
        $sql_delete = '';
        foreach ($this->other_fields_tables as $other_fields_table) {
            $sql_delete .= " DELETE FROM " . $other_fields_table['table_name'] . " WHERE ";
            foreach ($other_fields_table['other_fields'] as $other_field) {
                if ($other_field['is_key']) {
                    $sql_delete .= $other_field['field_name'] . ' = ? AND ';
                    $params_delete[] = $other_field['value'];
                }
            }
            $sql_delete .= '1 = 1;';
        }
        Database::edit($sql_delete, $params_delete);
    }

}
