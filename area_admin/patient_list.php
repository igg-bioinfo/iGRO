<?php

//--------------------------------VARIABLES
$trs = '';
$cols = [];
$has_arm = class_exists('Randomization_' . Config::RAND_CLASS);


//--------------------------------PATIENTS SELECT
$dias = [];
$oDiagnosis = Diagnosis::get_all();
foreach ($oDiagnosis as $oDia) {
    $dias[] = [$oDia->id, $oDia->name_short];
}
$cols[] = ["id" => 'patient_id', "label" => Language::find('patient_code'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'export_id', "label" => Language::find('export_code'), "type" => Field::TYPE_STRING, "orderable" => true];
$cols[] = ["id" => 'id_diagnosis', "label" => Language::find('diagnosis'), "type" => Paging_table::TYPE_MULTISELECT, 
    "values" => $dias, "default_value" => array_column($dias, 0), "orderable" => true];
$oPaging = new Paging_table('pt_table', $cols);
$oPatients = Patient_list::get_all(0, $oPaging);


//--------------------------------PATIENTS TABLE
$thead = HTML::set_tr(
    HTML::set_td(Language::find('patient_code'), '', true) .
    HTML::set_td(Language::find('export_code'), '', true) .
    HTML::set_td(Language::find('diagnosis'), '', true) .
    HTML::set_td(Language::find('age'), '', true) .
    ($has_arm ? HTML::set_td(Language::find('arm'), '', true) : '').
    HTML::set_td(Language::find('ongoing'), '', true) .
    HTML::set_td('', '', true), true
);
foreach ($oPatients as $oPaz) {
    URL::changeable_var_add('pid', $oPaz->id);
    $button_common = '';
    $button_common .= HTML::set_button(Icon::set_info().Language::find('patient_index'), '', URL::create_url('patient_index'));
    $bgcolor = !$oPaz->is_discontinued() && !$oPaz->has_visits() ? 'fff3cd' : ($oPaz->has_visits_not_confirmed() ? 'FFA79C' : 'd4edda');
    $button_common .= HTML::set_button(Icon::set_list().Language::find('visits').' ('.$oPaz->visits_confirmed.'/'.$oPaz->visits.')', '', URL::create_url('visits'), '', '', '', $bgcolor);
    $oRand = $has_arm ? Randomization::get_by_paz($oPaz->id) : NEW Randomization(NULL);
    //ROWS
    $trs .= HTML::set_tr(
        HTML::set_td($oPaz->patient_id) .
        HTML::set_td($oPaz->export_id) .
        HTML::set_td($oPaz->dia_short) .
        HTML::set_td($oPaz->get_age(). ' '.Language::find('years') ) .
        ($has_arm ? HTML::set_td($oRand->arm_text) : '').
        HTML::set_td(Icon::set_checker(!$oPaz->is_discontinued()) . HTML::set_spaces(2) . Date::object_to_screen($oPaz->date_end), '', false, '', 'text-align: center; ') .
        HTML::set_td($button_common) 
    );
}

$js = ' language: {
        "emptyTable": "' . Language::find('no_row_found') . '",
        search: "' . Language::find('search') . '",
        lengthMenu: "' . Language::find('DT_lengthMenu') . '",
        info: "' . Language::find('DT_info') . '",
        paginate: {
            next: "' . Language::find('next') . '",
            previous: "' . Language::find('previous') . '"
        }
    } ';
$html .= $oPaging->set($thead . HTML::set_tbody($trs), $js);


//--------------------------------------HTML--------------------------------------//
HTML::$title = Language::find('patients');
HTML::print_html(HTML::set_form(HTML::set_bootstrap_cell($html, 12)));
