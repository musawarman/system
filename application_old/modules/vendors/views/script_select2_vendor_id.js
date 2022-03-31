$(".vendor-id-select").select2({
    placeholder: "<?php _trans('vendor'); ?>",
    ajax: {
        url: "<?php echo site_url('vendors/ajax/name_query'); ?>",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                query: params.term,
                permissive_search_vendors: $('input#input_permissive_search_vendors').val(),
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
