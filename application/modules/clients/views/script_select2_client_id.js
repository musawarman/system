$(".client-id-select").select2({
    placeholder: "<?php _trans('client'); ?>",
    ajax: {
        url: "<?php echo site_url('clients/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
                permissive_search_clients: $('input#input_permissive_search_clients').val(),
                page: params.page,
                _ip_csrf: Cookies.get('ip_csrf_cookie')
            };
        },
        processResults: function (data) {
            console.log(data);
            return {
                results: data
            };
        },
        cache: true
    },
    minimumInputLength: 2
});

$(".vendor-id-select").select2({
    placeholder: "<?php _trans('client'); ?>",
    ajax: {
        url: "<?php echo site_url('vendors/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
                permissive_search_clients: $('input#input_permissive_search_clients').val(),
                page: params.page,
                _ip_csrf: Cookies.get('ip_csrf_cookie')
            };
        },
        processResults: function (data) {
            console.log(data);
            return {
                results: data
            };
        },
        cache: true,
    },
    minimumInputLength: 2,
    templateResult: tagResultTemplater,
    templateSelection: tagSelectionTemplater
});
function tagResultTemplater(tag) {
    return tag.text;
}
function tagSelectionTemplater(tag) {
    $('#vendor_id').val(tag.id);
    return tag.text;
}