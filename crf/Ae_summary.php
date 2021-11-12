<?php
class Ae_summary extends Abstract_CRF_page {
    private $form = "";
    private $oPrevVis = NULL;
    private $oAE_form = NULL;
    private $id_occurance = 0;
    private $id_occurance_name = 'id_occurrence';
    private $ae_list = [];
    private $wr_list = [];
    const RANGE_DAYS = 0;


    //------------------------OVERRIDES
    protected function init() {
        parent::init();
        $this->oAE_form = new Form('ae_occurrences', $this->oVisit->id);
        Language::add_area($this->oAE_form->class);
        $this->oAE_form->add_key_field_for_all('id_paz', $this->oPatient->id);
        $this->oAE_form->add_key_field_for_all($this->id_occurance_name, $this->id_occurance, true);
        $this->oAE_form->get_values();
        $this->ae_list = Adverse_event::get_list();
        $this->wr_list = Adverse_event::get_water_retention();
        HTML::add_link('crf/ae_summary', 'js');
    }

    protected function set_nav_buttons() {
        if ($this->is_view){
            return;
        }
        $this->html .= HTML::set_button(Icon::set_save() . Language::find('add'), "$('#has_ae').val('1'); $('#ae_act').val('ae_add'); page_validation('form1');", '', '', 'float: right;');
    }
    protected function process() {
        $ae_act = Security::sanitize(INPUT_POST, 'ae_act');
        if ($ae_act.'' != '') {
            $this->action = $ae_act;
        }
        switch ($this->action) {
            case 'edit':
            case 'view':
                $this->draw();
                break;
            case 'save':
                $this->validate();
                $this->save();
                $this->redirect();
                break;
            case 'ae_add':
                foreach ($this->oAE_form->oFields as $oField) {
                    $this->oAE_form->oFields[$oField->name]->value = $this->oAE_form->validate_field($oField);
                }
                $this->oAE_form->save();
                $this->map_form->is_completed = false;
                $this->map_form->page_current = 0;
                $this->map_form->save_form_status();
                //$this->map_form->delete();
                $this->redirect_form(1);
                break;
                case 'ae_remove':
                $ae_remove = Security::sanitize(INPUT_POST, 'ae_remove');
                if ($ae_remove.'' != '') {
                    Database::edit("DELETE FROM ae_occurrences WHERE id_occurrence = ?", [$ae_remove]);
                    $this->map_form->is_completed = false;
                    $this->map_form->page_current = 0;
                    $this->map_form->save_form_status();
                    //$this->map_form->delete();
                }
                $this->redirect_form(1);
                break;
        }
    }
    
    //------------------------DRAW
    function get_draw_custom_html($page_number) {
        if ($this->is_view) {
            $this->draw_table();
        } else {
            $this->draw_table();
            $this->draw_form();
        }
        return $this->form;
    }

    private function draw_form() {
        $this->form .= Form_input::createHidden('ae_act');
        $this->form .= Form_input::createHidden('ae_remove');
        $oField = $this->get_draw_field_object('has_ae');
        $this->form .= Form_input::createHidden($oField->name, $oField->value ?? 0);

        $this->form .= Form_input::set_normal_html(HTML::set_paragraph(Language::find('add_new')));
        $this->form .= Form_input::createHidden($this->id_occurance_name, $this->id_occurance);
        
        $wr_html = '<div id="group_wr" style="display: none;">';
        foreach($this->wr_list as $wr) {
            $wr_html .= Form_input::createCheckbox($wr[1], Language::find($wr[1]), '', 12, true, "check_wrs();", $this->is_view);
        }
        $wr_html .= '</div>';

        $oField = $this->oAE_form->get_valued_field('ae_other');
        $oth_html = '<div id="group_other" style="display: none; width: 100%;">';
        $oth_html .= Form_input::createInputText($oField->name, Language::find('other_specify'), $oField->value, 6, true, "check_text('".$oField->name."');", '', $this->is_view);
        $oth_html .= '</div>'.Form_input::br();

        $oField = $this->oAE_form->get_valued_field('ae_id');
        $this->form .= Form_input::createSelect($oField->name, Language::find($oField->name), $this->ae_list, $oField->value, 8, true, 
            "check_select('".$oField->name."'); check_ae()", $this->is_view);

        $this->form .= $wr_html.$oth_html;
        
        $this->oPrevVis = new Visit();
        $this->oPrevVis->get_previous($this->oPatient->id, $this->oVisit->date, true);
        $date_min = Date::default_to_screen($this->oPrevVis->date);
        $date_max = Date::add_days($this->oVisit->date, self::RANGE_DAYS);
        $date_max = Date::default_to_screen($date_max);

        $oField = $this->oAE_form->get_valued_field('ae_date');
        $js = "check_dates('".$date_min."', '".$date_max."'); ";
        $this->form .= Form_input::createDatePicker($oField->name, Language::find('date'), $oField->value, 3, true, $js, $this->is_view);

        $oField = $this->oAE_form->get_valued_field('serious');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes'), $oField->value, 1, 2, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 2, true, "check_radio('".$oField->name."');", $this->is_view);
        
        $oField = $this->oAE_form->get_valued_field('intensity');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('light'), $oField->value, 1, 2, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('moderate'), $oField->value, 2, 2, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('grave'), $oField->value, 3, 2, true, "check_radio('".$oField->name."');", $this->is_view);

        $oField = $this->oAE_form->get_valued_field('therapy_relation');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('probable'), $oField->value, 1, 2, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('probable_not'), $oField->value, 0, 2, true, "check_radio('".$oField->name."');", $this->is_view);

        $oField = $this->oAE_form->get_valued_field('therapy_discontinuation');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes'), $oField->value, 1, 2, false, "check_radio('".$oField->name."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 2, true, "check_radio('".$oField->name."');", $this->is_view);

        $oField = $this->oAE_form->get_valued_field('resolved_date');
        $js = "check_resolved('".$date_min."', '".$date_max."'); ";
        $resolved_date = Form_input::createDatePicker($oField->name, Language::find('date'), $oField->value, 3, false, $js, $this->is_view);

        $oField = $this->oAE_form->get_valued_field('resolved');
        $this->form .= Form_input::createLabel($oField->name, Language::find($oField->name));
        $this->form .= Form_input::createRadio($oField->name, Language::find('yes'), $oField->value, 1, 2, false, "check_radio('".$oField->name."'); check_resolved('".$date_min."', '".$date_max."');", $this->is_view);
        $this->form .= Form_input::createRadio($oField->name, Language::find('no'), $oField->value, 0, 2, true, "check_radio('".$oField->name."'); check_resolved('".$date_min."', '".$date_max."');", $this->is_view);

        $this->form .= '<div id="group_resolved" style="display: none; width: 100%;">'.$resolved_date.'</div>';
    }

    private function draw_table() {
        $text = str_replace('{0}', '<b>'.Language::find('ae_id') .'</b>', Language::find('delete_confirmation', ['validation']));
        $this->form .= Form_input::createPopup('ae_delete', Language::find('delete').' '.Language::find('ae_id'), $text, Language::find('delete'), 
            "$('#ae_act').val('ae_remove'); $('#form1').submit();", Language::find('no'));
        $this->form .= Form_input::set_normal_html(HTML::set_paragraph($this->oAE_form->get_title()));
        $no_ae = !isset($this->oAE_form->oValues) || count($this->oAE_form->oValues) == 0;
        if ($no_ae) {
            $this->form .= Form_input::set_normal_html(Language::find('no_ae'));
            if (!$this->is_view) {
                $this->form .= HTML::set_button(Icon::set_save() . Language::find('no_ae_btn'), "$('#has_ae').val('0'); $('#form1').submit();", '', '', 'margin: auto;');
            }
            return;
        }
        $trs = '';
        $thead = HTML::set_tr(
            HTML::set_td(Language::find('ae_id'), '', true) .
            HTML::set_td(Language::find('date'), '', true) .
            HTML::set_td(Language::find('serious'), '', true) .
            HTML::set_td(Language::find('intensity'), '', true) .
            HTML::set_td(Language::find('therapy_relation'), '', true) .
            HTML::set_td(Language::find('therapy_discontinuation'), '', true) .
            HTML::set_td(Language::find('resolved'), '', true) .
            HTML::set_td('', '', true), true
        );
        foreach ($this->oAE_form->oValues as $id => $oRow) {
            $ae = $oRow['ae_other']->value ?? Adverse_event::get_text($oRow['ae_id']->value);
            foreach ($oRow as $key => $field) {
                if (Strings::startsWith($key, 'wr_') && $field->value == 1) {
                    $ae .= '<br>- '.strtolower(Language::find($key));
                }
            }
            $buttons = '';
            if (!$this->is_view) {
                $buttons .= HTML::set_button(Icon::set_remove().Language::find('delete'), 
                    "$('#ae_remove').val('".$oRow['id_occurrence']->value."'); $('#ae_delete').modal('show');");    
            }
            $trs .= HTML::set_tr(
                HTML::set_td($ae) .
                HTML::set_td(Date::default_to_screen($oRow['ae_date']->value)) .
                HTML::set_td(Language::find($oRow['serious']->value.'' == '1' ? 'yes' : 'no')) .
                HTML::set_td($this->get_intensity($oRow['intensity']->value)) .
                HTML::set_td($this->get_therapy_relation($oRow['therapy_relation']->value)) .
                HTML::set_td(Language::find($oRow['therapy_discontinuation']->value.'' == '1' ? 'yes' : 'no')) .
                HTML::set_td(Icon::set_checker($oRow['resolved']->value.'' == '1'). ' '.Date::default_to_screen($oRow['resolved_date']->value)) .
                HTML::set_td($buttons) 
            );
        }
        $js = 'columnDefs: [
            {orderable: false, targets: [7]},
            {className: "responsive-table-dynamic-column", "targets": [0,1,2,3,4,5]}
            ], '.JS::set_responsive_lang().' ';
        $this->form .= HTML::set_bootstrap_cell(HTML::set_table_responsive($thead . HTML::set_tbody($trs), 'fields', $js), 12);
        if (!$this->is_view) {
            $this->form .= HTML::set_button(Icon::set_save() . Language::find('ae_btn'), 
                "$('#has_ae').val('1'); $('#form1').submit();", '', '', 'margin: auto;');
        }
    }

    private function get_intensity($id) {
        $text = '';
        if ($id.'' == '1') {
            $text = Language::find('light');
        } else if ($id.'' == '2') {
            $text = Language::find('moderate');
        } else if ($id.'' == '3') {
            $text = Language::find('grave');
        }
        return $text;
    }

    private function get_therapy_relation($id) {
        $text = '';
        if ($id.'' == '1') {
            $text = Language::find('probable');
        } else if ($id.'' == '0') {
            $text = Language::find('probable_not');
        }
        return $text;
    }
    
}
