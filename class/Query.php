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
    public $class = '';
    public $author = 0;
    public $ludati = null;


    private static $classes = CONFIG::QUERY_CLASSES;

    //-----------------------------------------------------STATIC
    static function get_all_by_visit($oVisit) {
        $oQueries = [];
        foreach (self::$classes as $class){
            $query_class = 'Query_'.$class;
            $oQuery = new $query_class($oVisit);
            $oQuery->class = 'Query_'.$class;
            if ($oQuery->has_issue()) {
                $oQueries[] = $oQuery;
            }
        }
        return $oQueries;
    }
    static function save_all($oVisit) {
        $oQueries = [];
        foreach (self::$classes as $class){
            $query_class = 'Query_'.$class;
            $oQuery = new $query_class($oVisit);
            $oQuery->class = 'Query_'.$class;
            if ($oQuery->has_issue()) {
                $oQuery->save();
                $oQueries[] = $oQuery;
            } else {
                $oQuery->delete();
            }
        }
        return $oQueries;
    }
    static function get_all() {
        global $oUser;
        $params = [];
        //echo $oUser->oCenter->id;
        $rows = Database::read("SELECT *, Q.ludati FROM query Q 
            INNER JOIN ".Patient::get_default_select()." P ON P.id_paz = Q.id_paz 
            INNER JOIN ".Visit::get_default_select()." V ON V.id_visita = Q.id_visita "
            .($oUser->oCenter->id != 0 ? "WHERE P.id_center = ".$oUser->oCenter->id : ""), 
            $params);
        $objects = [];
        foreach($rows as $row) {
            $oVisit = new Visit($row);
            $oQuery = new Query($oVisit);
            $oQuery->set_by_row($row);
            $objects[] = ['visit' => $oVisit, 'query' => $oQuery];
        }
        return $objects;
    }

    //-----------------------------------------------------OVERRIDES
    protected function init() {
        $this->sql = "";
        $this->params = [];
    }
    protected function set_by_row($row) {
        $this->description = $row['description'];
        $this->action = $row['action'];
        $this->author = isset($row['author']) ? $row['author'] : 0;
        $this->ludati = isset($row['ludati']) ? $row['ludati'] : null;
        $this->has_issue = true;
    }

    //-----------------------------------------------------CONSTRUCT & METHODS
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

    function save() {
        global $oUser;
        $params = [$this->id_paz, $this->id_visita, $this->class];
        $rows = Database::read("SELECT * FROM query WHERE id_paz = ? AND id_visita = ? AND query_class = ? ", $params);
        if (count($rows) == 0) {
            $params[] = $this->is_blocking ? 1 : 0;
            $params[] = $this->description;
            $params[] = $this->action;
            $params[] = $oUser->id;
            Database::edit("INSERT INTO query (id_paz, id_visita, query_class, is_blocking, description, action, author, ludati) 
                SELECT ?, ?, ?, ?, ?, ?, ?, NOW();", $params);
        } else {
            $params = [$this->is_blocking ? 1 : 0, $this->description, $this->action, $oUser->id,
                $this->id_paz, $this->id_visita, $this->class];
            Database::edit("UPDATE query SET is_blocking = ?, description = ?, action = ?, author = ?, ludati = NOW()
            WHERE id_paz = ? AND id_visita = ? AND query_class = ? ", $params);
        }
    }

    function delete() {
        $params = [$this->id_paz, $this->id_visita, $this->class];
        Database::edit("DELETE FROM query WHERE id_paz = ? AND id_visita = ? AND query_class = ? ", $params);
    }
}