<?php

abstract class Abstract_CRF_basic {

    protected $html = '';                     // Stores all generated html code, used for final printing by output function
    protected $page_number = 0;               // Current page number retrieved from URL (page)
    protected $action = '';              // Action value retrieved from POST (act)
    protected $is_view = false;               // Indicates whether the form is in view (read-only) mode (action type "view")
    protected $oUser = NULL;
    protected $oPatient = NULL;
    protected $oVisit = NULL;
    protected $oArea = NULL;
    protected $oMain_form = NULL;             // Map form object of Map_form class
    protected $main_key_name = '';
    protected $main_key_value = 0;
    protected $main_key_url = '';
    protected $has_visit_block = true;
    protected $pk_array = [];

    const POST_ACT = 'post_act';
    const ACT_SAVE = 'save';
    const ACT_NEXT = 'next';
    const ACT_BACK = 'back';

    abstract protected function custom_post();

    abstract protected function custom_draw();

    protected function init() {
        $this->get_request_variables();
        $this->get_main_objects();
        $this->get_main_form();

        //---JAVASCRIPT DEFAULT VARIABLES
        HTML::$js .= " var page_number = '" . $this->page_number . "'; ";
    }

    // Assigns URL variables
    protected function get_request_variables() {
        $this->page_number = URL::get_onload_var('page') != '' ? URL::get_onload_var('page') : 0;
        $this->action = Security::sanitize(INPUT_POST, self::POST_ACT);
        $this->is_view = URL::get_onload_var('act') == 'view';
        $this->main_key_value = URL::get_onload_var($this->main_key_url) != '' ? URL::get_onload_var($this->main_key_url) : 0;
    }

    // Assigns object properties
    protected function get_main_objects() {
        global $oPatient, $oVisit, $oArea, $oUser;
        $this->oVisit = $oVisit;
        $this->oArea = $oArea;
        $this->oPatient = $oPatient;
        $this->oUser = $oUser;
    }

    // Creates a map form object to get access to all form fields
    protected function get_main_form() {
        $form_id = URL::get_onload_var('fid');
        $this->oMain_form = new Form();
        //PAGE_NUMBER MANAGEMENT
        $this->oMain_form->get_by_id($form_id);

        $this->oMain_form->set_main_value($this->oMain_form->is_visit_related ? $this->oVisit->id : $this->oPatient->id);
        if ($this->main_key_name . '' != '') {
            $this->oMain_form->add_key_field_for_all($this->main_key_name, $this->main_key_value, true);
        }
    }

    //--------------------------HTML ELEMENTS
    protected function set_visit_block() {
        HTML::set_detail_block($this->oPatient, $this->oVisit);
    }

    protected function set_html_title() {
        HTML::$title = $this->oMain_form->get_title();
    }

    protected function set_html_last_author($use_custom_author = false, $author_id = NULL, $ludati = NULL) {
        //echo json_encode($this->oMain_form);
        if (!$use_custom_author) {
            $author_id = isset($this->oMain_form->author) ? $this->oMain_form->author : null;
            $ludati = isset($this->oMain_form->ludati) ? $this->oMain_form->ludati : null;
        }
        if (isset($author_id) && isset($ludati)) {
            $oAuthor = new User();
            $oAuthor->get_by_id($author_id);
            HTML::set_audit_trail($oAuthor, $ludati);
        }
        global $oArea;
        if ($oArea->id == Area::$ID_ADMIN) {
            HTML::set_audit_archive($this->pk_array);
        }
    }

    protected function set_html_progressbar($page_number, $page_max) {
        if ($this->is_view || $page_max == 0) {
            return;
        }

        $page_percentage = $page_number / $page_max * 100;
        return '<div class="progress" style="width: 200px; height: 25px; margin: 0 auto">'
                . '<div class="progress-bar" role="progressbar" style="width: ' . $page_percentage . '%"><span style="font-size: 15px">' . $page_number . '/' . $page_max . '</span></div>'
                . '</div>';
    }

    //--------------------------HTML BUTTONS
    protected function set_form_act($form) {
        $form .= Form_input::createHidden(self::POST_ACT, '');
        $this->html .= HTML::set_form($form);
    }

    protected function set_button_back($do_validate = true, $form_tag = 'form1') {
        $button = '<div style="float: left;">';
        $button .= HTML::set_button(($do_validate ? Icon::set_save() : Icon::set_back()) . 'Back', "$('#" . self::POST_ACT . "').val('" . self::ACT_BACK . "'); " . ($do_validate ? "page_validation('" . $form_tag . "');" : "$('#" . $form_tag . "').submit();"));
        $button .= '</div>';
        return $button;
    }

    protected function set_button_next($do_validate = true, $form_tag = 'form1') {
        $button = HTML::set_button(($do_validate ? Icon::set_save() : Icon::set_next()) . 'Next', "$('#" . self::POST_ACT . "').val('" . self::ACT_NEXT . "'); " . ($do_validate ? "page_validation('" . $form_tag . "');" : "$('#" . $form_tag . "').submit();"), '', '', 'float:right;');
        return $button;
    }

    protected function set_button_save($form_tag = 'form1') {
        $button = HTML::set_button(Icon::set_save() . 'Save', "$('#" . self::POST_ACT . "').val('" . self::ACT_SAVE . "'); page_validation('" . $form_tag . "');", '', '', 'float:right;');
        return $button;
    }

    protected function redirect_exit() {
        URL::changeable_vars_reset();
        URL::changeable_var_add('pid', $this->oPatient->id);
        URL::changeable_var_add('vid', $this->oVisit->id);
        URL::redirect('visit_index', '');
    }

    protected function set_button_exit() {
        URL::changeable_vars_reset();
        URL::changeable_var_add('pid', $this->oPatient->id);
        URL::changeable_var_add('vid', $this->oVisit->id);
        $button = '<div style="float: left;">';
        $button .= HTML::set_button(Icon::set_back() . Language::find('visit'), '', URL::create_url('visit_index', ''));
        $button .= '</div>';
        return $button;
    }

    protected function set_modify_button() {
        //add modify button on the right only in view pages, not locked and in Admin and investigator area
        if ($this->is_view && !$this->oVisit->is_lock && ($this->oArea->id == Area::$ID_ADMIN || $this->oArea->id == Area::$ID_INVESTIGATOR)) {
            URL::changeable_vars_from_onload_vars();
            URL::changeable_var_add('act', 'edit');
            $this->html .= HTML::set_button(Icon::set_edit() . Language::find('modify'), '', URL::create_url(Globals::FORM_URL, ''), '', 'float: right;');
        }
    }

    //--------------------------RENDER
    public function render() {
        $this->init();
        if (Security::sanitize(INPUT_SERVER, 'REQUEST_METHOD') === 'POST') {
            $this->custom_post();
        }
        $this->custom_draw();
        if ($this->has_visit_block) {
            $this->set_visit_block();
        }
        HTML::print_html($this->html);
    }

}

?>