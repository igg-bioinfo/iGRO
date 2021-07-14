function back(page_id) {
    var new_page = parseInt($('#' + page_id).val()) - 1;
    if (new_page > 0) {
        $('#' + page_id).val(new_page);
    }
}

function next(page_id, max_page_numbers) {
    var new_page = parseInt($('#' + page_id).val()) + 1;
    if (new_page <= max_page_numbers) {
        $('#' + page_id).val(new_page);
    }
}

function select_page(page_id) {
    $('#' + page_id).val(parseInt($('#' + page_id + '_select').val()));
    $('#form1').submit();
}

function header_callback(thead, data, start, end, display) {
    var table_id = this[0].id;

    var column_mapping = JSON.parse($('#' + table_id + '_colmapping').val());

    var ths = $(thead).find('th').filter(function (index) {
        if (column_mapping.indexOf(index) !== -1) {
            $(this).removeClass('sorting_disabled').addClass('sorting');
            return true;
        }
    });

    if ($('#' + table_id + '_order').val()) {
        var order = JSON.parse($('#' + table_id + '_order').val());
        $(thead).find('th').eq(order[0][0]).removeClass('sorting').addClass('sorting_' + order[0][1]);
    }
    ths.click(function () {
        var order_direction = 'asc';
        if ($(this).hasClass('sorting_asc')) {
            order_direction = 'desc';
        }

        ths.removeClass('sorting_asc sorting_desc').addClass('sorting');
        $(this).removeClass('sorting').addClass('sorting_' + order_direction);
        create_hidden_field(table_id + '_order', JSON.stringify([[this.cellIndex, order_direction]]));
        $('#form1').submit();
    });
}

function filter_validation() {
    var errors = $("#form1 .invalid-feedback:visible");
    if (errors.length > 0) {
        $("#form1 .alert").show();
        return false;
    }
    $('#form1').submit();
}


