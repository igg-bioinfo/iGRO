function check_dates() {
    var date_birth = parseDate($('#date_birth').val());
    var date_diagnosis = parseDate($('#date_diagnosis').val());
    var date_first_visit = parseDate($('#date_first_visit').val());
    
    var invalid = false;
    if(check_text('date_birth', 11) && !check_date('date_birth')) { invalid = true; }
    if(check_text('date_diagnosis', 11) && !check_date('date_diagnosis')) { invalid = true; }
    if(check_text('date_first_visit', 11) && !check_date('date_first_visit')) { invalid = true; }
    if (invalid) return; 
    
    if (date_birth > date_diagnosis) {
        change_status('date_birth', 2, DATES_INCONGRUENCE);
        change_status('date_diagnosis', 2, DATES_INCONGRUENCE);
    } else if (date_birth > date_first_visit) {
        change_status('date_birth', 2, DATES_INCONGRUENCE);
        change_status('date_first_visit', 2, DATES_INCONGRUENCE);
    } else {
        if ($('#date_birth').val() == '') return;
        if ($('#date_first_visit').val() == '') return;
        if ($('#date_diagnosis').val() == '') return;
        change_status('date_birth', 1);
        change_status('date_first_visit', 1);
        change_status('date_diagnosis', 1);
    }
}
