<?php

abstract class Abstract_CRF_page {

    protected $form_id;                         // Form id retrieved from URL (fid)
    protected $visit_id;                        // Visit id retrieved from URL (vid)
    protected $action;                          // Action value retrieved from URL (act), possible values: edit, view, save
    protected $button_nav;                      // Navigation button value retrieved from hidden input field (button_nav), possible values: back, next
    protected $map_form;                        // Map form object of Map_form class
    protected $form_title;                      // Form title value taken from Map_form object (title)
    protected $drawn_fields_array;              // Array for counting the number of fields used in drawing on one page
    protected $fields_total;                    // Total number of fields to be drawn on one (=current) page
    protected $debug_info;                      // Stores debug information printed by init.
    protected $html;                            // Stores all generated html code, used for final printing by output function
    protected $page_number;                     // Current page number retrieved from URL (page)
    protected $max_page;                        // Max page (or last page) value taken from Map_form object (page_last)
    protected $is_view;                         // Indicates whether the form is in view (read-only) mode (action type "view")
    protected $is_upload;                       // Indicates whether the form is with a file upload input

    const PRINT_DEBUG_INFO = false;             // When TRUE page specific debug information will be printed
    const RUN_PAGE_CHECKS = false;              // When TRUE page specific checks are enabled and form will be tested for certain drawing errors
    const PRINT_WITH_ECHO = false;              // When TRUE debug info will be printed by echo, when FALSE will be printed as part of HTML
    const PRINT_INPUT_NAMES = false;            // When TRUE field names will be added and printed in HTML code
    const ADD_BOOTSTRAP_ROW_IN_FORM = true;     // When TRUE <div class="row"> will be added inside <form> (called by set_form)

    // bottom page button labels

    protected static $BUTTON_LABEL_NEXT = 'Next';
    protected static $BUTTON_LABEL_PREV = 'Back';
    protected static $BUTTON_LABEL_SAVE = 'Save';
    protected static $BUTTON_LABEL_MODIFY = 'Modify';
    protected static $BUTTON_LABEL_RETURN = 'Visit index';
    // urls for redirection
    protected static $REDIRECT_URL_SAVE = Globals::FORM_URL;
    protected static $REDIRECT_URL_EXIT = 'visit_index';
    //variables used in visit block
    protected $oPatient = NULL;
    protected $oVisit = NULL;
    protected $oArea = NULL;


    protected $pk_array = [];

    // Constructor, no arguments
    public function __construct() {
        $this->get_request_variables();
        $this->html = '';
        $this->is_upload = false;
        $this->debug_info = '';
        $this->max_page = -1;
        $this->fields_total = -1;
        $this->form_title = '';
        $this->drawn_fields_array = [];
    }

    // Assigns URL variables to object fields
    protected function get_request_variables() {
        $this->page_number = URL::get_onload_var('page') != '' ? URL::get_onload_var('page') : 1;
        $this->form_id = URL::get_onload_var('fid');
        $this->visit_id = URL::get_onload_var('vid');
        $this->action = URL::get_onload_var('act') != '' ? URL::get_onload_var('act') : 'edit';

        if (Security::sanitize(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
            // mark action as "save" if POST request found
            $this->action = 'save';
            // read navigation button value from the hidden field
            $this->button_nav = Security::sanitize(INPUT_POST, 'button_nav');
        }

        // this flag must be set before calling get_form_fields!
        $this->is_view = $this->action == 'view' ? true : false;
    }

    // Creates a map form object to get access to all form fields
    protected function get_form_fields() {
        $this->map_form = new Form();

        if ($this->is_view) {
            // crf forms in view mode do not support paging, so all form fields must be retrieved
            $this->map_form->get_by_id($this->form_id);
        } else {
            // for non-view crf forms retrieve only page specific fields
            $this->map_form->get_by_id($this->form_id, $this->page_number);
        }

        $main_key_value = $this->map_form->is_visit_related ? $this->visit_id : $this->oVisit->id_paz;
        $this->map_form->set_main_value($main_key_value);
    }

    // Retrieves current form values
    protected function get_form_values() {
        $this->map_form->get_values();
    }

    // Sets visit block data variables
    protected function set_visit_block_variables() {
        global $oPatient, $oVisit, $oArea;
        $this->oVisit = $oVisit;
        $this->oArea = $oArea;
        $this->oPatient = $oPatient;
    }

    // Sets visit block data HTML for the CRF page
    protected function set_visit_block() {
        HTML::set_detail_block($this->oPatient, $this->oVisit);
    }

    // Sets JS data for CRF page
    protected function set_js_data() {
        $page_num = $this->page_number;
        if ($this->is_view) {
            $page_num = -1;
        }
        HTML::$js .= 'var page_number = ' . $page_num . ';';
    }

    // Post constructor initialization
    protected function init() {
        // this has to be called first, so "get_form_fields" would have access to id_paz
        $this->set_visit_block_variables();
        $this->get_form_fields();
        $this->get_form_values();
        $this->form_title = $this->map_form->title;
        $this->max_page = $this->map_form->page_last;
        $this->fields_total = count($this->map_form->oFields);
        Language::add_area('form');
        Language::add_area($this->map_form->class);
        self::$BUTTON_LABEL_NEXT = Language::find('next');
        self::$BUTTON_LABEL_PREV = Language::find('back');
        self::$BUTTON_LABEL_SAVE = Language::find('save');
        self::$BUTTON_LABEL_MODIFY = Language::find('modify');
        self::$BUTTON_LABEL_RETURN = Language::find('forms');
        $this->set_visit_block();
        $this->set_js_data();

        // "self::" is not inheritance aware, "static::" has to be used here
        if (static::PRINT_DEBUG_INFO) {
            $is_view = ($this->is_view) ? 'True' : 'False';

            $this->debug_info .= '<br>';
            $this->debug_info .= "FORM CLASS=" . get_class($this) . '<br>';
            $this->debug_info .= "FORM ID=" . $this->form_id . '<br>';
            $this->debug_info .= "VISIT ID=" . $this->visit_id . '<br>';
            $this->debug_info .= "ACTION=" . $this->action . '<br>';
            $this->debug_info .= "PAGE NUMBER=" . $this->page_number . '<br>';
            $this->debug_info .= "PAGE MAX=" . $this->max_page . '<br>';
            $this->debug_info .= "BUTTON NAV=" . $this->button_nav . '<br>';
            $this->debug_info .= "IS_VIEW=" . $is_view . '<br>';
            $this->debug_info .= "TOTAL FIELDS ON PAGE=" . $this->fields_total . '<br>';
            $this->debug_info .= '<br>';

            if (static::PRINT_WITH_ECHO) {
                echo $this->debug_info;
            } else {
                $this->html .= $this->debug_info;
            }
        }

        // Raise error if no fields found for the current form/page
        if (!$this->fields_total) {
            $this->show_error_page(7780);
        }
    }

    // Redirects to the error page
    // Argument: error code defined in Messages class
    protected function show_error_page($error_code) {
        URL::redirect('error', $error_code);
    }

    // Main processing function, handles different action types and calls corresponding methods
    protected function process() {
        switch ($this->action) {
            case 'edit':
                $this->draw();
                break;
            case 'view':
                $this->draw();
                break;
            case 'save':
                $this->before_validate_action();
                $this->validate();
                $this->before_save_action();
                $this->save();
                $this->after_save_action();
                $this->redirect();
                break;
        }
    }

    // Draws the complete page by calling start, custom, and end methods
    protected function draw() {
        $this->draw_common_start();

        if ($this->is_view) {
            $custom_html = $this->get_draw_custom_html_view();
        } else {
            $custom_html = $this->get_draw_custom_html($this->page_number);
        }

        $this->draw_custom($custom_html);
        $this->draw_common_end();
        $this->check_total_fields_drawn();
    }

    // Draws top page elements common to all forms
    protected function draw_common_start() {
        $this->set_crf_title();
        $this->set_last_update_info();
        $this->set_audit_archive();
    }

    // Draws custom form content returned by get_draw_custom_html function.
    // Parameters: $form_html - should be valid HTML code
    protected function draw_custom($form_html) {
        // do not print form tags (<form>...</form>) for crf forms in view mode
        if ($this->is_view) {
            if (static::ADD_BOOTSTRAP_ROW_IN_FORM) {
                $this->html .= HTML::set_row($form_html);
            } else {
                $this->html .= $form_html;
            }
        } else {
            //add navigation buttons to form data, this has to be done before calling set_form
            $form_html .= $this->set_nav_info();
            // Sadly, PHP does not support named parameters, so all function argument have to be repeated below
            $this->html .= HTML::set_form($form_html, 'form1', '', 'post', '', static::ADD_BOOTSTRAP_ROW_IN_FORM, $this->is_upload);
            // original parameters values from HTML::set_form (Sept. 2018):
            // set_form($html, $id = 'form1', $other = '', $method = 'post', $action = '', $add_row = true, $is_upload = false)
        }
    }

    // Abstract function which has to be defined in subclasses.
    // This function should return valid HTML code, which will be then placed inside <form>...</form> tags
    // Parameters: $page_number - page number of crf form to be drawn
    abstract protected function get_draw_custom_html($page_number);

    // This is a wrapper function used for drawing crf forms in view mode.
    // By default it calls "get_draw_custom_html" but it can be overwritten if customized drawing is required.
    protected function get_draw_custom_html_view() {
        return $this->get_draw_custom_html($this->page_number);
    }

    // Draws bottom page elements common to all crf forms
    protected function draw_common_end() {
        $this->html .= HTML::BR;
        $this->set_progressbar();
        $this->html .= HTML::BR;
        $this->set_nav_buttons();
        $this->set_exit_button();
        $this->set_modify_button();
        $this->html .= HTML::BR;
    }

    // Internal check function: checks if field page number (taken from SQL) matches the page number on which field is drawn
    protected function check_page_number($oField) {
        // do not check page numbers in view mode (as all fields belong to page 1)
        if ($this->is_view)
            return;

        $field_page_number = $oField->page_number;
        if ($field_page_number != $this->page_number) {
            $this->show_error_page(7778);
        }
    }

    // Internal check function: checks if all fields assigned to page (taken from SQL) have been used in the drawing process
    protected function check_total_fields_drawn() {
        if (!static::RUN_PAGE_CHECKS)
            return;

        // check the total number of fields drawn
        if (count($this->drawn_fields_array) != $this->fields_total) {
            $this->show_error_page(7779);
        }
    }

    // Returns one map field object (single field) used for drawing.
    // Parameters: $field_name - SQL name of form/table field
    protected function get_draw_field_object($field_name) {
        $oField = $this->map_form->get_valued_field($field_name);
        if (!$oField) {
            $this->show_error_page(7782);
        }

        if (static::RUN_PAGE_CHECKS) {
            $this->check_page_number($oField);

            // update array of fields used in drawing
            // this is used instead of a counter to check if all fields have been used correctly
            if (array_key_exists($field_name, $this->drawn_fields_array)) {
                // error , field used twice
                $this->show_error_page(7781);
            } else {
                $this->drawn_fields_array[$field_name] = 1;
            }
        }
        return $oField;
    }

    // Returns an array (or an associative array) of map field objects by calling get_draw_field_object internally
    // Parameters: $fields - an array of strings
    protected function get_draw_field_object_array($fields, $assoc = false) {
        $field_obj_array = [];
        if (is_array($fields)) {
            foreach ($fields as $field) {
                $field_obj = $this->get_draw_field_object($field);
                if ($assoc) {
                    $field_obj_array[$field] = $field_obj;
                } else {
                    array_push($field_obj_array, $field_obj);
                }
            }
        }
        return $field_obj_array;
    }

    // Custom action called before validating CRF
    protected function before_validate_action() {

    }

    // Validates all input fields using type based rules and required status.
    // Validation rules are embedded into map_form
    protected function validate() {
        foreach ($this->map_form->oFields as $oField) {
            $this->map_form->oFields[$oField->name]->value = $this->map_form->validate_field($oField);
        }
    }

    // Custom action called before saving CRF
    protected function before_save_action() {

    }

    // Saves crf form by calling map form save method
    // Also calls saving form status.
    protected function save() {
        $this->map_form->save();

        //saves form status: it's completed if it's the last page
        if (isset($this->map_form->main_value) && $this->map_form->page_current != 0) {
            $this->save_form_status();
        }
    }

    // saves form status
    protected function save_form_status() {
        $this->map_form->is_completed = $this->map_form->is_completed || $this->page_number == $this->max_page;
        $this->map_form->save_form_status();
    }

    // Custom action called after saving CRF
    protected function after_save_action() {

    }

    // Redirects after saving (POST submission)
    protected function redirect() {
        $to_page = 1;

        // if last page, redirect to visit index (but only when user did not click 'back')
        if ($this->page_number == $this->max_page && $this->button_nav != 'back') {
            $this->redirect_index();
        }
        // else read which button (back or next) has been clicked
        else {
            if ($this->button_nav == 'next') {
                $to_page = $this->page_number + 1;
            } elseif ($this->button_nav == 'back') {
                $to_page = $this->page_number - 1;
            }

            // when page number is out of range, assign page 1
            if ($to_page < 1 || $to_page > $this->max_page)
                $to_page = 1;
            // redirect to form
            $this->redirect_form($to_page);
        }
    }

    // redirect action when form is not finished
    protected function redirect_form($to_page) {
        URL::changeable_var_add('page', $to_page);
        URL::redirect(self::$REDIRECT_URL_SAVE);
    }

    // redirect action when form is finished
    protected function redirect_index() {
        // visit index does not need 'page' and 'act' url variables
        URL::changeable_var_remove('page');
        URL::changeable_var_remove('act');
        URL::redirect(self::$REDIRECT_URL_EXIT);
    }

    // Prints complete crf form page HTML code
    protected function output() {
        HTML::print_html($this->html);
    }

    // Adds CRF title information to the generated page
    protected function set_crf_title() {
        HTML::$title = Language::find($this->form_title);
    }

    // Adds last update information to the generated page
    protected function set_last_update_info() {
        $author = $this->map_form->author;
        $ludati = $this->map_form->ludati;
        if ($author && $ludati) {
            $user = new User();
            $user->get_by_id($author);
            HTML::set_audit_trail($user, $ludati, $this->oPatient->id, $this->oArea->id);
        }
    }

    // Adds search for archive changes
    protected function set_audit_archive() {
        global $oArea;
        if ($oArea->id == Area::$ID_ADMIN) { // && $this->map_form->is_completed
            //HTML::set_audit_archive($this->pk_array);
        }
    }

    // Adds hidden field with navigation button information to html form
    protected function set_nav_info() {
        return Form_input::createHidden('button_nav', '');
    }

    // Adds progress bar to forms with paging
    protected function set_progressbar() {
        // no progress bar for one-page forms and forms in view mode
        if ($this->is_view || $this->max_page == 1)
            return;
        //echo $this->max_page; exit;
        $page_percentage = $this->page_number / $this->max_page * 100;
        $this->html .= '<div class="progress" style="width: 200px; height: 25px; margin: 0 auto">
                            <div class="progress-bar" role="progressbar" style="width: ' . $page_percentage . '%"><span style="font-size: 15px">' . $this->page_number . '/' . $this->max_page . '</span></div>
                        </div>';
    }

    // Adds navigation buttons to forms. Forms with paging will receive Back/Next/Save buttons.
    // Forms without paging will receive Save button only.
    protected function set_nav_buttons() {
        // TODO: improve JS validation handling
        // no navigation buttons for crf forms in view mode
        if ($this->is_view)
            return;

        // show back button only for pages greater than 1
        if ($this->page_number > 1) {
            //$button_label = Icon::set_save() . self::$BUTTON_LABEL_PREV;
            //$this->html .= HTML::set_button($button_label, "document.getElementById('button_nav').value='back'; page_validation('form1');");
            URL::changeable_vars_reset_except(['pid', 'vid', 'fid']);
            URL::changeable_var_add('page', $this->page_number - 1);
            URL::changeable_var_add('act', 'edit');
            $this->html .= HTML::set_button(Icon::set_save() . self::$BUTTON_LABEL_PREV, '', URL::create_url(Globals::FORM_URL, ''));
        }

        // save/next button is always present
        if ($this->page_number < $this->max_page) {
            $button_label = Icon::set_save() . self::$BUTTON_LABEL_NEXT;
            $this->html .= HTML::set_button($button_label, "document.getElementById('button_nav').value='next'; page_validation('form1');", '', '', 'float: right;');
        } else {
            $button_label = Icon::set_save() . self::$BUTTON_LABEL_SAVE;
            $this->html .= HTML::set_button($button_label, "page_validation('form1');", '', '', 'float: right;');
        }
    }

    // Adds exit button (visit_index page link) to all crf forms
    protected function set_exit_button() {
        // remove page variable when going to visit index page
        //URL::changeable_var_remove('page');
        URL::changeable_vars_reset_except(['pid', 'vid']);
        $this->html .= HTML::set_button(Icon::set_back() . self::$BUTTON_LABEL_RETURN, '', URL::create_url('visit_index', ''), '', 'float: left;');
    }

    // Adds modify button (visit_index page link) to all crf forms
    protected function set_modify_button() {
        //add modify button on the right only in view pages, not locked and in Admin and investigator area
        if ($this->is_view && !$this->oVisit->is_lock && ($this->oArea->id == Area::$ID_ADMIN || $this->oArea->id == Area::$ID_INVESTIGATOR)) {
            URL::changeable_vars_reset_except(['pid', 'vid', 'fid']);
            URL::changeable_var_add('page', '1');
            URL::changeable_var_add('act', 'edit');
            $this->html .= HTML::set_button(Icon::set_edit() . self::$BUTTON_LABEL_MODIFY, '', URL::create_url(Globals::FORM_URL, ''), '', 'float: right;');
        }
    }

    // The only public function, called to display complete crf form/page after object creation
    public function render() {
        $this->init();
        $this->process();
        $this->output();
    }

}

?>