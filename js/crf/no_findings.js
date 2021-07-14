function check_one_click(id, finding_name) {
    if (finding_name === undefined) {
        finding_name = 'no_findings';
    }
    if ($('#' + id).prop('checked')) {
        if (id == finding_name) {
            $('input:checkbox').prop('checked', false);
            $('#' + finding_name).prop('checked', true);
        } else {
            $('#' + finding_name).prop('checked', false);
        }
    }
    if ($('input:checkbox').is(':checked')) {
        $("#error_" + finding_name).html("");
        $("#status_" + finding_name).attr("class", "form-group valid-feedback");
    } else {
        $("#error_" + finding_name).html(SELECT_ONE_OPTION);
        $("#status_" + finding_name).attr("class", "invalid-feedback");
    }
}