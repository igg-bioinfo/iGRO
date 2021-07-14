<?php
class Field {
    public $id = 0;
    public $name = '';
    public $type = 0;
    public $description = '';
    public $table_name = '';
    public $is_extra_field = NULL; 
    public $limit_min = NULL;
    public $limit_max = NULL;
    public $order_id = 1;
    public $page_number = 1;
    public $required = NULL;
    public $value = NULL;
    public $force_required = false;
    
    const TYPE_BOOL = 1;
    const TYPE_INT = 2;
    const TYPE_STRING = 3;
    const TYPE_DATE = 4;
    const TYPE_FLOAT = 5;
    const TYPE_BOOL_0 = 6;
    const TYPE_BOOL_99 = 7;

    private static $list_type = [
        [self::TYPE_BOOL, 'BOOL'],
        [self::TYPE_INT, 'INTEGER'],
        [self::TYPE_STRING, 'STRING'],
        [self::TYPE_DATE, 'DATE'],
        [self::TYPE_FLOAT, 'FLOAT'],
        [self::TYPE_BOOL_0, 'BOOL with zero as null'],
        [self::TYPE_BOOL_99, 'BOOL with 99 as null']
    ];
    
    function __construct($field = []) {
        if (count($field) > 0) {
            $this->set_by_row($field);
        }
    }

    public function get_by_id($id, $form_id = 0) {
        $params = [$id];
        if ($form_id != 0) { $params[] = $form_id; }
        $field_list = Database::read("SELECT F.* FROM ".self::get_table()." F 
            WHERE F.field_id = ? ".($form_id != 0 ? "AND F.form_id = ?" : ""), $params);
        if (count($field_list) > 0) {
            $this->set_by_row($field_list[0]);
        }
    }
    
    public function set_by_row($field){
        $this->id = $field['field_id'];
        $this->name = $field['field_name'];
        $this->type = $field['field_type'];
        $this->description = $field['field_description'];
        $this->table_name = $field['table_name'];
        $this->is_extra_field = $field['is_extra_field'].'' == '1';
        $this->limit_min = $field['limit_min'];
        $this->limit_max = $field['limit_max'];

        $this->order_id = $field['order_id'];
        $this->page_number = $field['page_number'];
        $this->required = $field['required'];
    }

    public function get_name(){
        return Language::find($this->name, [$this->table_name]);
    }

    public function get_type(){
        foreach(self::$list_type as $type) {
            if ($this->type == $type[0]) {
                return $type[1];
            }
        }
        return '';
    }

    public function save($form_id){
        if ($this->id == 0) {
            $this->create($form_id);
        } else {
            $this->update();
        }
        $this->manage_db();
    }

    private function create($form_id){
        $params = [$this->name, $this->table_name, $this->type, $this->is_extra_field, 
            $this->description, $this->limit_min, $this->limit_max];
        $sql = "INSERT INTO field (field_name, table_name, field_type, is_extra_field,
            field_description, limit_min, limit_max) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
        $this->id = Database::edit($sql, $params, true);
        
        $params = [$form_id, $this->id, $this->page_number, $this->order_id, $this->required];
        $sql = "INSERT INTO field_form (form_id, field_id, page_number, order_id, required) 
            VALUES (?, ?, ?, ?, ?)";
        Database::edit($sql, $params);

    }

    private function update(){
        $params = [$this->name, $this->table_name, $this->type, $this->is_extra_field, 
            $this->description, $this->limit_min, $this->limit_max, $this->id];
        $sql = "UPDATE field SET field_name = ?, table_name = ?, field_type = ?, is_extra_field = ?,
            field_description = ?, limit_min = ?, limit_max = ? 
            WHERE field_id = ? ";
        Database::edit($sql, $params);
    }

    private function get_db_type() {
        $type = '';
        switch($this->type) {
            case self::TYPE_BOOL:
            case self::TYPE_BOOL_0:
            case self::TYPE_BOOL_99:
                $type .= " smallint(6) ";
                break;
            case self::TYPE_INT:
                $type .= " INT ";
                break;
            case self::TYPE_STRING:
                if ($this->limit_max.'' == '' || $this->limit_max.'' == '0'){
                    $type .= " TEXT ";
                } else {
                    $type .= " varchar(".$this->limit_max.") ";
                }
                break;
            case self::TYPE_DATE:
                $type .= " DATETIME ";
                break;
            case self::TYPE_FLOAT:
                $type .= " FLOAT ";
                break;
        }
        $type .= ($this->required.'' == '1' ? ' NOT' : '').' NULL';
        return $type;
    }

    private function manage_db(){
        Database::$as_admin = true;
        $field_db = $this->is_extra_field ? "`extra_fields` text DEFAULT NULL" : "`".$this->name."` ".$this->get_db_type();
        if (!Database::table_exists($this->table_name)) {
            $sql = "CREATE TABLE `".$this->table_name."` (
                ".$field_db.",
                `author` bigint(20) NOT NULL,
                `ludati` datetime NOT NULL,
                `id_visita` bigint(20) NOT NULL,
                `id_paz` bigint(20) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            Database::edit($sql, []);
        } else if (!Database::field_exists($this->table_name, $this->is_extra_field ? "extra_fields" : $this->name)) {
            $sql = "ALTER TABLE ".$this->table_name." ADD ".$field_db.";";
            Database::edit($sql, []);
        }
        Database::$as_admin = false;
    }

    public static function get_types(){
        return self::$list_type;
    }

    public static function get_table(){
        return "(
            SELECT F.*, FF.form_id, FF.page_number, FF.order_id, FF.required 
            FROM field F 
            LEFT OUTER JOIN field_form FF ON FF.field_id = F.field_id
        )";
    }
}
