
function check_changed() {
    visibility('new_reason', true);
    visibility('new_dose', true);
    visibility('new_injection', true);
    if ($('#therapy_change_0').is(':checked')) {
        clear_changed();
        $('#label_new_reason').hide();
        $('#new_reason').hide();
        $('#therapy_changed').hide();
    
        $('#date_start').val($('#prev_date').val());
        $('#dose').val($('#prev_dose').val());
        $('#injection').val($('#prev_injection').val());
        $('#reason').val($('#prev_reason').val());
    } else if ($('#therapy_change_1').is(':checked')) {
        clear_changed();
        $('#therapy_changed').show();
        $('#label_new_reason').hide();
        $('#new_reason').hide();
    } else if ($('#therapy_change_2').is(':checked')) {
        clear_changed();
        $('#therapy_changed').show();
        visibility('new_reason', false);
    } else if ($('#therapy_change_3').is(':checked')) {
        clear_changed();
        $('#therapy_changed').show();
        $('#new_reason').show();
        visibility('new_dose', false);
        visibility('new_injection', false);
    }
}
function visibility(id, show) {
    if (show) {
        $('#' + id).show();
        $('#label_' + id).show();
        $('#error_' + id).show();
        $('#status_' + id).show();
    } else {
        $('#' + id).hide();
        $('#label_' + id).hide();
        $('#error_' + id).hide();
        $('#status_' + id).hide();
    }
}
function clear_changed() {
    $('#new_date_start').val('');
    $('#new_dose').val('');
    $('#new_injection').val('');
    $('#new_reason').val('');

    check_date('new_date_start',!$('#therapy_change_0').is(':checked'));
    check_number('new_dose', !$('#therapy_change_3').is(':checked') && !$('#therapy_change_0').is(':checked'));
    check_integer('new_injection','','', !$('#therapy_change_3').is(':checked') && !$('#therapy_change_0').is(':checked'));
    check_text('new_reason','','','', $('#therapy_change_3').is(':checked'));
    
    $('#date_start').val('');
    $('#dose').val('');
    $('#injection').val('');
    $('#reason').val('');
}

function dose_change(){
    if (check_number('new_dose', true)) {
        if ($('#therapy_change_1').is(':checked') && $('#new_dose').val() <= $('#prev_dose').val()) {
            change_status('new_dose', 2, DOSE_UP);
        } else if ($('#therapy_change_2').is(':checked') && $('#new_dose').val() >= $('#prev_dose').val()) {
            change_status('new_dose', 2, DOSE_DOWN);
        } else {
            $('#dose').val($('#new_dose').val());
        }
    }
}

function date_change(date_min, date_max){
    if (check_date_min_max('new_date_start',true, date_min, date_max)) {
        $('#date_start').val($('#new_date_start').val());
    }
}

function injection_change() {
    if (check_integer('new_injection','5','8', true)) {
        $('#injection').val($('#new_injection').val());
    }
}

function reason_change() {
    if (check_text('new_reason','','','', false)) {
        $('#end_reason').val($('#new_reason').val());
    }
}

function check_continuity() {
    var not_checked = $("input[name='therapy_continuity']:checked").val() === undefined || $("#therapy_continuity_0").is(":checked");
    $("[id^=no_cont_][type=checkbox]").each(function() {
        if (not_checked) {
            $(this).prop('checked', false);
        } else {
            change_status($(this).name, 0, "");
        }
        $(this).prop( "disabled", not_checked);
    });
    check_yes_why();
}

function check_yes_why() {
    if ($("input[name='therapy_continuity']:checked").val() === undefined || $("#therapy_continuity_0").is(":checked")) {
        return;
    }
    var not_chk = $("[id^=no_cont_][type=checkbox]:checked").val() === undefined;
    $("[id^=no_cont_][type=checkbox]").each(function() {
        var element = $(this)[0];
        if (not_chk) {
            change_status(element.name, 2, SELECT_ONE_OPTION);
        } else {
            change_status(element.name, 1, "");
        }
    });
}

function check_yes_specify() {
    if ($("#no_cont_other").is(":checked")) {
        $("#no_cont_specify").prop( "disabled", false);
        check_text('no_cont_specify');
    } else {
        change_status('no_cont_specify', 0, "");
        $("#no_cont_specify").val('');
        $("#no_cont_specify").prop( "disabled", true);
    }
}

function check_injector() {
    if ($("#injector_2").is(":checked")) {
        $("input[name='patient_injector']").prop( "disabled", false);
        check_radio('patient_injector');
    } else {
        $("input[name='patient_injector']").prop('checked', false);
        $("input[name='patient_injector']").each(function() {
            var element = $(this)[0];
            change_status(element.id, 0, "");
        });
        $('#error_patient_injector').attr('class', 'valid-feedback');
        $("input[name='patient_injector']").prop( "disabled", true);
    }
}