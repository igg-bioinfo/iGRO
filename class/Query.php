<?php

class Query {
    protected $id_paz = 0;
    protected $id_visita = 0;
    protected $sql = '';
    protected $params = [];
    protected $has_issue = false;
    public $is_blocking = false;
    public $description = '';
    public $action = '';


    private static $classes = CONFIG::QUERY_CLASSES;

    //-----------------------------------------------------STATIC
    static function get_all($oVisit) {
        $oQueries = [];
        foreach (self::$classes as $class){
            $query_class = 'Query_'.$class;
            $oQuery = new $query_class($oVisit);
            if ($oQuery->has_issue()) {
                $oQueries[] = $oQuery;
            }
        }
        return $oQueries;
    }

    //-----------------------------------------------------OVERRIDES
    protected function init() {
        $this->sql = "";
        $this->params = [];
    }
    protected function set_by_row($row) {
        $this->description = $row['description'];
        $this->action = $row['action'];
        $this->has_issue = true;
    }

    //-----------------------------------------------------CONSTRUCT & INTERFACE METHODS
    function __construct($oVisit) {
        $this->id_paz = $oVisit->id_paz;
        $this->id_visita = $oVisit->id;
        $this->init();
    }

    function has_issue() {
        $rows = Database::read($this->sql, $this->params);
        if (count($rows) > 0) {
            $this->set_by_row($rows[0]);
            return $this->has_issue;
        }
        return false;
    }
}