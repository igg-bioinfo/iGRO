<?php

class Ajax_autocompleter {

    protected $request_params;                   // term/phrase entered by the user in meddra search field and sent in GET request as "av" param
    protected $cleaned_request_params;           // cleaned GET param, removed unwanted characters
    protected $sql;                              // sql command to be built
    protected $sql_params;                       // sql parameters for sql command
    protected $output_array;                     // container for JSON formatted meddra data
    protected $sql_template;                     // meddra SQL query, must be provided when object is created, and must include a placeholder (#)
    private $db_name = '';
    private $db_field = '';

    const AUTO_ID = 'id';
    const AUTO_LABEL = 'value';
    const SQL_REPLACE_WHERE = '#';
    //const SQL_REPLACE_FIELDS = '$';
    const NOT_FOUND = 'No results found';

    // Constructor, one argument: sql query template, 
    // which should use const SQL_REPLACE_WHERE for where field and const AUTO_ID and AUTO_LABEL for field alias
    public function __construct($sql_template, $dbfield_where, $dbname = '') {
        $this->get_request_variables();
        $this->output_array = [];
        $this->sql = '';
        $this->db_name = $dbname;
        $this->db_field = $dbfield_where;
        $this->sql_params = [];
        $this->sql_template = $sql_template;
    }

    // Assigns URL variables to object fields
    // Here URL variables are not encrypted
    protected function get_request_variables() {
        $this->request_params = $_GET['av'];
    }

    // Removes unwanted characters from query params
    protected function clean_request_params() {
        $out_str = preg_replace('/[^A-Za-z0-9]/', ' ', trim($this->request_params));
        $out_str = preg_replace('/ {2,}/', ' ', trim($out_str));
        $this->cleaned_request_params = trim($out_str);
    }

    // Builds SQL statement based on query parameters provided. 
    // For instance for "av=head drum" it will build the code:
    //      AND ( LOW_LEVEL_TERM.LLT_NAME LIKE ? AND LOW_LEVEL_TERM.LLT_NAME LIKE ? ) 
    // with 2 parameters: sql_params[0]=%head%, sql_params[1]=%drum%
    protected function build_sql_query() {
        $request_params_array = [];

        // explode returns non-empty array for empty string, so the check for empty string is added first
        if ($this->cleaned_request_params) {
            $request_params_array = explode(" ", $this->cleaned_request_params);
        }
        $request_params_array_count = count($request_params_array);

        if ($request_params_array_count && $this->sql_template) {
            $sql_insert = ' ( ';
            for ($i = 0; $i < $request_params_array_count; $i++) {
                $sql_insert .= ' ' . $this->db_field . ' LIKE ? ';
                if ($i != ($request_params_array_count - 1))
                    $sql_insert .= " AND ";
                $this->sql_params[] = '%' . $request_params_array[$i] . '%';
            }
            $sql_insert .= ' ) ';

            # insert built SQL statement into placeholder in sql_template
            $this->sql = str_replace(self::SQL_REPLACE_WHERE, $sql_insert, $this->sql_template);
        }
    }

    // Runs SQL statement and saves retrieved data into output_array
    protected function run_sql_query() {
        if (!$this->sql)
            return;

        $rows = Database::read($this->sql, $this->sql_params, $this->db_name);
        foreach ($rows as $row) {
            $data_row[self::AUTO_ID] = $row[self::AUTO_ID];
            $data_row[self::AUTO_LABEL] = $row[self::AUTO_LABEL];
            $this->output_array[] = $data_row;
        }
    }

    // Returns JSON data or error message in JSON format
    protected function return_json_data() {
        if (!count($this->output_array)) {
            $this->output_array['error'] = self::NOT_FOUND;
        }
        echo json_encode($this->output_array);
    }

    // Public function called after object creation
    public function run() {
        $this->clean_request_params();
        $this->build_sql_query();
        $this->run_sql_query();
        $this->return_json_data();
    }

}
