<?php

class Query_test extends Query {
    public $is_blocking = false;

    protected function init() {
        $this->sql = "SELECT 'TEST QUERY' as description, '-' as action;";
        $this->params = [];
    }
}