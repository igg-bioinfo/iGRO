// Helper function which allows common style string formatting. Usage:
// String.format("My name is {0} and I live in {1}.", 'Fidel', 'Cuba') returns: "My name is Fidel and I live in Cuba."
if (!String.format) {
    String.format = function (format) {
        var args = Array.prototype.slice.call(arguments, 1);
        return format.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined'
                    ? args[number]
                    : match
                    ;
        });
    };
}

function page_validation(submitter) {
    // check if skip controls variable is defined and true
    if (typeof skip_controls !== 'undefined' && skip_controls === true) {
        $("#" + submitter).submit();
        return true;
    }

    // otherwise do normal form validation
    var errors = $("#" + submitter + " .invalid-feedback:visible");
    if (errors.length > 0) {
        $("#table_warning").hide(); $("#table_ok").hide();
        $("#table_error").show();
        $("#table_text_error").html(ERROR_PRESENT);
        window.scrollTo(0, $("#table_text_error").offset().top);
        return false;
    }
    $("#" + submitter).submit();
}

function popup_validation(submitter) {
    var errors = $("#" + submitter + " .invalid-feedback:visible");
    if (errors.length > 0) {
        errors.each(function () {
            var element = $(this);
            element.attr('data-placement', 'left');
            set_tooltip(element, ERROR_PRESENT);
            element.tooltip('show');

        });
        return false;
    }
    $("#" + submitter).submit();
}


$(document).ready(function () {
    $(window).keydown(function (event) {
        if (event.keyCode === 13) {
            if (event.target.type === "textarea") {
            } else {
                event.preventDefault();
                return false;
            }
        }
    });
});

// for view pages, in order to remove all validation error
function page_clear() {
    var errors = $(".invalid-feedback:visible");
    for (c = 0; c < errors.length; c++) {
        change_status(errors[c].id.replace('status_', ''), 0, '');
    }
}

function set_error(status_error, error_text) {
    status_error.html("&nbsp;");
    if (error_text !== "") {
        status_error.html(error_text);
    }
}

function set_tooltip(input, error_message) {
    input.prop('title', error_message);
    if (error_message) {
        input.tooltip('dispose');
        input.attr('data-toggle', 'tooltip');
        input.tooltip();
    } else {
        input.tooltip('disable');
    }
}

function change_status(id, status, error) {
    var status_border = $("#status_" + id);
    var status_error = $("#error_" + id);

    var type = $("#" + id).attr('type');

    // set error
    if (status_error.length) { // not basic
        set_error(status_error, error);
        if (type === 'checkbox') {
            status_border = status_error;
        }
    } else {
        switch (type) {
            case 'radio':
                var radio_prefix = id.substring(0, id.lastIndexOf("_"));
                var last_radio_item = $("[name='" + radio_prefix + "']").last().closest('.form-check');
                if (last_radio_item.length) { // not basic
                    status_error = $("#error_" + radio_prefix);
                    if (!status_error.length) {
                        last_radio_item.after('<div id="error_' + radio_prefix + '" style="margin-left:15px; width: 100%; margin-bottom: 15px;">&nbsp;</div>');
                        status_error = $("#error_" + radio_prefix);
                    }
                    set_error(status_error, error);
                    status_border = status_error;
                } else { // basic
                    var radio_button = $('#' + id).parent('.form-check-label').find('.radio_checkmark');

                    // set_tooltip(radio_button, error);
                }
                break;
            case 'checkbox':
                var checkbox_prefix = id.split("_")[0];
                var last_checkbox_item = $("[id^='" + checkbox_prefix + "']").last().closest('.form-check');
                if (last_checkbox_item.length) { //not basic
                    status_error = $("#error_" + checkbox_prefix);
                    if (!status_error.length) {
                        last_checkbox_item.after('<div id="error_' + checkbox_prefix + '" style="margin-left:15px; width: 100%; margin-bottom: 15px;">&nbsp;</div>');
                        status_error = $("#error_" + checkbox_prefix);
                    }
                    set_error(status_error, error);
                    status_border = status_error;
                } else { // basic
                    var checkbox = $('#' + id).parent('.form-check-label').find('.checkmark');

                    set_tooltip(checkbox, error);
                }
                break;
            default:
                // basic
                if (error === '&nbsp;') {
                    error = '';
                }

                set_tooltip($("#" + id), error);
                break;
        }
    }

    var status_border_class = "";

    $("#" + id).prop("disabled", false);

    // For radios and check-boxes the border is created and removed.
    // For all other objects only the border color is changed, but the border itself stays all the time at 1px.

    // Neutral status, no borders drawn
    if (status === 0) {
        status_border_class = status_error.length ? '' : 'basic';
        $('#' + id).css('border-color', '');
        //if (!status_error.length) {
        $('#' + id).parent('.form-check-label').find('.radio_checkmark').css('border', '');
        $('#' + id).parent('.form-check-label').find('.checkmark').css('border', '');
        //}
        // OK status, draws green border (but not for radios and check-boxes)
    } else if (status === 1) {
        status_border_class = status_error.length ? 'valid-feedback' : 'basic valid-feedback';
        $('#' + id).css('border-color', 'green');
        //if (!status_error.length) {
        $('#' + id).parent('.form-check-label').find('.radio_checkmark').css('border', '');
        $('#' + id).parent('.form-check-label').find('.checkmark').css('border', '');
        //}
        // Error status, draws red border
    } else if (status === 2) {
        status_border_class = status_error.length ? 'invalid-feedback' : 'basic invalid-feedback';
        $('#' + id).css('border-color', 'red');
        //if (!status_error.length) {
        $('#' + id).parent('.form-check-label').find('.radio_checkmark').css('border', 'solid red 1px');
        $('#' + id).parent('.form-check-label').find('.checkmark').css('border', 'solid red 1px');
        //}
        // Makes object disabled
    } else if (status === 4) {
        status_border_class = status_error.length ? 'testogrigio' : 'basic testogrigio';
        $('#' + id).css('border-color', '');
        $("#" + id).prop("disabled", true);
        //if (!status_error.length) {
        $('#' + id).parent('.form-check-label').find('.radio_checkmark').css('border', '');
        $('#' + id).parent('.form-check-label').find('.checkmark').css('border', '');
        //}
    }

    if (status_border_class) {
        status_border.attr('class', status_border_class);
    }
    if (status_error.length) { // not basic
        status_border.show();
    } else { // basic
        $('#' + id).parent('.form-check-label').find('.radio_checkmark').attr('class', 'radio_checkmark ' + status_border_class);
        $('#' + id).parent('.form-check-label').find('.checkmark').attr('class', 'checkmark ' + status_border_class);

        // if input is basic, use inline instead of block
        status_border.css('display', 'inline');
    }


    //-----HIDE ERROR RED BLOCK IF NO ERROR AND THERE'S NO JAVASCRIPT ERRORS
    if ($("#table_text_error").html() != DISABLED_SUBMIT) {
        var errors = $(".invalid-feedback:visible");
        if (errors.length == 0) {
            $("#table_error").hide();
            $("#table_text_error").html('');
        }
    }
}

function required_check(id, correct, value, required) {
    if (correct && !value && required) { // empty and required
        change_status(id, 2, REQUIRED_FIELD);
        correct = false;
    }
    if (correct && value) { // not empty and correct
        change_status(id, 1, "");
    }
    if (!value && !required) { // empty and not required
        change_status(id, 0, "");
    }
    return correct;
}

function handle_javascript_errors_on_page() {
    $("#table_warning").hide(); $("#table_ok").hide();
    $("#table_error").show();
    $("#table_text_error").html(DISABLED_SUBMIT);
    $("form").on("submit", function (event) {
        $("#table_error").show();
        $("#table_text_error").html(DISABLED_SUBMIT);
        event.preventDefault();
    });
}

function initializeMultiSelect(id) {
    $('#' + id).selectpicker();
}


/*------------------------- VALIDATION FUNCTIONS -----------------------------*/
function clear_text(id) {
    $("#" + id).val("");
    return true;
}

function check_text(id, lengthmin, lengthmax, lengthprecise, required, canStartWithNumber) {
    if (!canStartWithNumber) {
        canStartWithNumber = true;
    }
    if (!lengthmin) {
        lengthmin = '';
    }
    if (!lengthmax) {
        lengthmax = '';
    }
    if (!lengthprecise) {
        lengthprecise = '';
    }
    if (required === undefined) {
        required = true;
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;

    if (lengthmin !== "" && text.length < lengthmin) {
        change_status(id, 2, String.format(TEXT_MIN_LENGHT, lengthmin));
        correct = false;
    } else if (lengthmax !== "" && text.length > lengthmax) {
        change_status(id, 2, String.format(TEXT_MAX_LENGHT, lengthmax));
        correct = false;
    } else if (lengthprecise !== "" && text.length !== lengthprecise) {
        change_status(id, 2, String.format(TEXT_PRECISE_LENGHT, lengthprecise));
        correct = false;
    } else if (!isNaN(text.charAt(0)) && !canStartWithNumber) {
        change_status(id, 2, "This text can't start with a number.");
        correct = false;
    }

    return required_check(id, correct, text, required);
}

function check_integer(id, minvalue, maxvalue, required) {
    if (required === undefined) {
        required = false;
    }
    var numbercheck = $("#" + id).val().trim();
    var correct = true;

    if (numbercheck && numbercheck.length > 0) {
        var regex = /^\d+$/;
        if (!regex.test(numbercheck)) {
            change_status(id, 2, TEXT_INTEGER);
            correct = false;
        }
        if (minvalue && numbercheck <= minvalue) {
            change_status(id, 2, String.format(TEXT_NUMBER_MAJOR, minvalue));
            correct = false;
        }
        if (maxvalue && numbercheck >= maxvalue) {
            change_status(id, 2, String.format(TEXT_NUMBER_MINOR, maxvalue));
            correct = false;
        }
    }
    return required_check(id, correct, numbercheck, required);
}

function check_mail(id, required) {
    if (required === undefined) {
        required = false;
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;

    if (text && text.length > 0) {
        var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        //"
        if (!regex.test(text)) {
            change_status(id, 2, TEXT_EMAIL);
            correct = false;
        }
    }

    return required_check(id, correct, text, required);
}

function check_date(id, required) {
    if (required === undefined) {
        required = false;
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;
    if (text !== 'undefined' && text !== null && text.length > 0) {
        var date = moment(text, "DD-MMM-YYYY", true);
        if (!date.isValid()) {
            change_status(id, 2, TEXT_DATE);
            correct = false;
        }
    } //ok
    return required_check(id, correct, text, required);
}

function check_date_min_max(id, required, min, max) {
    if (required === undefined) {
        required = false;
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;
    if (text !== 'undefined' && text !== null && text.length > 0) {
        var date = moment(text, "DD-MMM-YYYY", true);
        if (!date.isValid()) {
            change_status(id, 2, TEXT_DATE);
            correct = false;
        } else {
            // date is valid
            if (min) {
                min = moment(min, "DD-MMM-YYYY", true);
                if (date.isBefore(min)) {
                    change_status(id, 2, String.format(TEXT_DATE_MINOR_EQUAL, min.format("DD-MMM-YYYY")));
                    correct = false;
                }
            }
            if (max) {
                max = moment(max, "DD-MMM-YYYY", true);
                if (date.isAfter(max)) {
                    change_status(id, 2, String.format(TEXT_DATE_MAJOR_EQUAL, max.format("DD-MMM-YYYY")));
                    correct = false;
                }
            }
        }
    }
    return required_check(id, correct, text, required);
}

function check_text_regex(id, regex, flags) {
    if (!flags) {
        flags = '';
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var patt;
    if (flags != '') {
        patt = new RegExp(regex, flags);
    } else {
        patt = new RegExp(regex);
    }
    if (!patt.test(text)) {
        change_status(id, 2, WRONG_FORMAT);
        return false;
    }
    change_status(id, 1, '');
    return true;
}

function check_editor(id, length) {
    var text = CKEDITOR.instances[id] === null ? "" : CKEDITOR.instances[id].getData().trim();
    var correct = true;
    change_status(id, (length === 0 ? 0 : 1), "");
    if (text !== 'undefined' && text !== null && text.length < length) {
        change_status(id, 2, String.format(TEXT_MIN_LENGHT, length));
        correct = false;
    }
    return correct;
}

function check_mails(id, required) {
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    change_status(id, 0, "");
    var correct = true;
    if (text !== 'undefined' && text !== null && text.length > 0) {
        var regex = /^[\W]*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,6}[\W]*,{1}[\W]*)*([\w+\-.%]+@[\w\-.]+\.[A-Za-z]{2,6})[\W]*$/;
        //"
        change_status(id, 1, "");
        if (!regex.test(text)) {
            change_status(id, 2, TEXT_EMAILS);
            correct = false;
        }
    }
    return required_check(id, correct, text, required);
}

function check_multi_select(id) {
    if ($('#' + id).is(':disabled')) {
        return true;
    }
    var index = $('#' + id + ' option:selected').length;
    var correct = true;
    if (index === 0) {
        change_status(id, 2, SELECT_ONE_OPTION);
        $('#' + id).selectpicker('setStyle', 'btn-error', 'add');
        correct = false;
    } else {
        change_status(id, 1, '');
        $('#' + id).selectpicker('setStyle', 'btn-error', 'remove');
    }
    $('#' + id).selectpicker('refresh');
    return correct;
}

function check_bootstrap_select(id, required) {
    if (required === undefined) {
        required = false;
    }
    var select = $('#' + id);
    var index = select[0].selectedIndex;
    var correct = true;
    change_status(id, 1, '');
    $("[data-id='" + id + "']").css('border-color', 'green');

    var no_selection_index;
    if (required === true) {
        no_selection_index = -1;
    } else {
        no_selection_index = 0;
    }
    if (index === no_selection_index) {
        change_status(id, 2, SELECT_ONE_OPTION);
        $("[data-id='" + id + "']").css('border-color', 'red');
        correct = false;
    }
    return correct;

}

function check_select(id, required) {
    if (required === undefined) {
        required = true;
    }
    var index = $("#" + id)[0].selectedIndex;
    var correct = true;
    if (required) {
        change_status(id, 1, "&nbsp;");
        if (index === 0) {
            change_status(id, 2, SELECT_ONE_OPTION);
            correct = false;
        }
    } else if (index !== 0) {
        change_status(id, 1, "&nbsp;");
    }
    return correct;
}

function check_radio(name) {
    var radio = $("[name='" + name + "']");
    var correct = false;
    for (r = 0; r < radio.length; r++) {
        if (radio[r].checked) {
            correct = true;
        }
    }

    for (r = 0; r < radio.length; r++) {
//        if ($('#' + radio[r].id).is(":disabled")) {
//            continue;
//        }
        if (!correct) {
            change_status(radio[r].id, 2, SELECT_ONE_OPTION);
        } else {
            change_status(radio[r].id, 1, "");
        }
    }

    return correct;
}

function check_checkbox(id, minchecked) {
    var checkbox = $("[id^='" + id.split("_")[0] + "']");
    var correct = 0;
    for (c = 0; c < checkbox.length; c++) {
        if (checkbox[c].checked) {
            correct++;
        }
    }

    for (c = 0; c < checkbox.length; c++) {
        if (correct < minchecked) {
            change_status(checkbox[c].name, 2, SELECT_ONE_OPTION);
        } else {
            change_status(checkbox[c].name, 1, "");
        }
    }

    return correct;
}

function check_checkboxes_array(checkboxes, minchecked, error_id) {
    if (!Array.isArray(checkboxes)) {
        checkboxes = JSON.parse(checkboxes);
    }
    var correct = 0;
    for (c = 0; c < checkboxes.length; c++) {
        if ($('#' + checkboxes[c]).is(":disabled")) {
            if (error_id) {
                var error = $('#' + error_id);
                set_error(error, '');
                error.removeClass('invalid-feedback');
            }
            return;
        }

        if ($('#' + checkboxes[c]).is(":checked")) {
            correct++;
        }
    }

    for (c = 0; c < checkboxes.length; c++) {
        if (correct < minchecked) {
            change_status(checkboxes[c], 2, SELECT_ONE_OPTION);
        } else {
            change_status(checkboxes[c], 1, "");
        }
    }

    if (error_id) {
        $('#error_' + checkboxes[0].split("_")[0]).remove();
        if (!correct) {
            var error = $('#' + error_id);
            set_error(error, SELECT_ONE_OPTION);
            error.addClass('invalid-feedback').show();
        } else {
            var error = $('#' + error_id);
            set_error(error, '');
            error.removeClass('invalid-feedback');
        }
    }

    return correct;
}

function check_year(id) {
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;
    change_status(id, 0, "");
    if (text !== 'undefined' && text !== null && text.length > 0) {
        var regex = /^\d{4}$/;
        //"
        change_status(id, 1, "");
        if (!regex.test(text)) {
            change_status(id, 2, TEXT_YEAR);
            correct = false;
        } else if (parseFloat(text) < 1901 || parseFloat(text) > 2099) {
            change_status(id, 2, TEXT_YEAR);
            correct = false;
        }
    }
    return correct;
}

function check_url(id) {
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;
    change_status(id, 0, "");
    if (text !== 'undefined' && text !== null && text.length > 0) {
        var regex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/;
        var regex = /((https?:\/\/|ftp:\/\/|www\.|[^\s:=]+@www\.).*?[a-z_\/0-9\-\#=&])(?=(\.|,|;|\?|\!)?("|'|ï¿½|ï¿½|\[|\s|\r|\n|$))/;
        //"
        change_status(id, 1, "");
        if (!regex.test(text)) {
            change_status(id, 2, TEXT_WEB);
            correct = false;
        }
    }
    return correct;
}

function check_number(id, required) {
    if (required === undefined) {
        required = true;
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;
    change_status(id, 0, "");
    if (isNaN(text.value) === true) {
        var regex = /^(-)?\d+(\.\d{1,4})?$/;
        change_status(id, 1, "");
        if (!regex.test(text)) {
            change_status(id, 2, TEXT_NUMBER);
            correct = false;
        }
    }
    return required_check(id, correct, text, required);
}

function check_number_unsigned(id) {
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var correct = true;
    change_status(id, 0, "");
    if (isNaN(text.value) === true) {
        var regex = /^(-)?\d+(\.\d{1,4})?$/;
        //"
        change_status(id, 1, "");
        if (!regex.test(text)) {
            change_status(id, 2, TEXT_NUMBER);
            correct = false;
        }
    }
    return correct;
}

function check_number_min_max_by_id(id, min_id, max_id, required, negative) {
    if (required === undefined) {
        required = true;
    }

    if (negative === undefined) {
        negative = false;
    }

    var correct = true;
    var min_value = $("#" + min_id).val();
    var max_value = $("#" + max_id).val();
    var is_min_number = false;
    var is_max_number = false;
    var regex = /^\d+(\.\d{1,4})?$/;

    if (negative) {
        regex = /^(-)?\d+(\.\d{1,4})?$/;
    }

    // check if are valid numbers
    if (min_value && min_value.length > 0) {
        if (regex.test(min_value)) {
            min_value = parseFloat(min_value);
            is_min_number = true;
        }
    }

    if (max_value && max_value.length > 0) {
        if (regex.test(max_value)) {
            max_value = parseFloat(max_value);
            is_max_number = true;
        }
    }

    if (is_min_number && is_max_number && min_value >= max_value) {
        if (id === min_id) {
            change_status(min_id, 2, String.format(TEXT_NUMBER_MINOR, max_value));
        }
        if (id === max_id) {
            change_status(max_id, 2, String.format(TEXT_NUMBER_MAJOR, min_value));
        }
        correct = false;
    }

    if (id === min_id) {
        if (!is_min_number && min_value.length > 0) {
            change_status(min_id, 2, TEXT_NUMBER);
            correct = false;
        }

        if (is_max_number && correct) {
            change_status(max_id, 1);
        }
    }

    if (id === max_id) {
        if (!is_max_number && max_value.length > 0) {
            change_status(max_id, 2, TEXT_NUMBER);
            correct = false;
        }

        if (is_min_number && correct) {
            change_status(min_id, 1);
        }
    }

    return required_check(id, correct, $("#" + id).val(), required);
}

function check_number_mineq_maxeq(id, minvalue, maxvalue, required) {
    var numbercheck = $("#" + id).val().trim();
    var correct = true;

    if (numbercheck.length > 0) {

        var regex = /^\d+(\.\d{1,4})?$/;
        if (!regex.test(numbercheck)) {
            change_status(id, 2, TEXT_NUMBER);
            correct = false;
            return correct;
        }

        if (minvalue && typeof (minvalue) === 'string') {
            minvalue = parseInt(minvalue);
        }

        if (maxvalue && typeof (maxvalue) === 'string') {
            maxvalue = parseInt(maxvalue);
        }

        if (numbercheck < minvalue) {
            change_status(id, 2, String.format(TEXT_NUMBER_MAJOR_EQUAL, minvalue));
            correct = false;
            return correct;
        }

        if (numbercheck > maxvalue) {
            change_status(id, 2, String.format(TEXT_NUMBER_MINOR_EQUAL, maxvalue));
            correct = false;
            return correct;
        }

    }
    return required_check(id, correct, numbercheck, required);
}

function check_time(id) {
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    change_status(id, 0, "");
    if (text.length > 0) {
        if (text.length !== 5)
            change_status(id, 2, TEXT_MINUTES);
        else {
            if (/^[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}$/.test(text) + '' === 'false')
                change_status(id, 2, TEXT_MINUTES);
        }
    }
}

function parseDate(date) {
    return new Date(date.replace(/-/gi, ' '));

}

function check_file(id, required) {
    if (required === undefined) {
        required = false;
    }
    var correct = false;
    var value = $('#' + id).val();
    if (value == '') {
        correct = true;
    } else {
        for (var i = 0; i < EXT_ALLOWED.length; i++) {
            if (value.toLowerCase().endsWith('.' + EXT_ALLOWED[i])) {
                correct = true;
                break;
            }
        }
    }
    if (!correct) {
        change_status(id, 2, EXT_ERROR);
        return false;
    }
    var file = document.getElementById(id).files[0];
    if (file !== undefined && file !== null) {
        if (file.size > SIZE_MAX) {
            change_status(id, 2, SIZE_ERROR);
            return false;
        }
    }
    return required_check(id, correct, value, required);
}

function check_password(id, regex, flags) {
    if (!flags) {
        flags = '';
    }
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }
    var patt;
    if (flags != '') {
        patt = new RegExp(regex, flags);
    } else {
        patt = new RegExp(regex);
    }
    if (!patt.test(text)) {
        change_status(id, 2, String.format(PASSWORD_FORMAT + ' (' + SPECIALS + ')', 10));
        return false;
    }
    change_status(id, 1, '');
    return true;
}

function check_password_confirm(id, id_to_compare) {
    var text = $("#" + id).val().trim();
    if (text != $("#" + id).val()) {
        change_status(id, 2, TEXT_EMPTY_SPACES);
        return false;
    }

    var text_to_compare = $("#" + id_to_compare).val().trim();

    if ($("#status_" + id_to_compare).hasClass("invalid-feedback")) {
        change_status(id, 2, MATCH_PASSWORD);
        return false;
    }

    if (text !== text_to_compare) {
        change_status(id, 2, MATCH_PASSWORD);
        return false;
    }
    if (!text) {
        change_status(id, 2, String.format(PASSWORD_FORMAT + ' (' + SPECIALS + ')', 10));
        return false;
    }
    change_status(id, 1, '');
    return true;
}