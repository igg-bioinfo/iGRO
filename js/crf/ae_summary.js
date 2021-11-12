function check_ae() {
    if ($('#ae_id').val() == '5') {
        $('#group_wr').show();
        check_wrs();
    } else {
        $('#group_wr').hide();
        $("[id^=wr_][type=checkbox]").each(function() {
            $(this).prop('checked', false);
            change_status($(this)[0].name, 1, "");
        });
    }
    if ($('#ae_id').val() == '99') {
        $('#group_other').show();
        check_text('ae_other');
    } else {
        $('#group_other').hide();
        $('#ae_other').val('');
        change_status('ae_other', 1, "");
    }
}

function check_wrs() {
    var not_chk = $("[id^=wr_][type=checkbox]:checked").val() === undefined;
    $("[id^=wr_][type=checkbox]").each(function() {
        var element = $(this)[0];
        if (not_chk) {
            change_status(element.name, 2, SELECT_ONE_OPTION);
        } else {
            change_status(element.name, 1, "");
        }
    });
}

function check_resolved(date_min, date_max) {
    if ($("#resolved_1").is(":checked")) {
        $("#group_resolved").show();
    } else {
        $("#group_resolved").hide();
        change_status('resolved_date', 1, "");
    }
    check_dates(date_min, date_max);
}

function check_dates(date_min, date_max) {
    if (check_date_min_max('ae_date', true, date_min, date_max)) {
        check_date_min_max('ae_date', true, undefined, $('#resolved_date').val());
    }
    if ($("#resolved_1").is(":checked")) {
        if (check_date_min_max('resolved_date', true, date_min, date_max)) {
            check_date_min_max('resolved_date', true, $('#ae_date').val());
        }
    }
}