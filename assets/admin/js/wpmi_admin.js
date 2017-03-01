(function($) {
    'use strict';

    $(document).ready(function($) {

        $(document).on('change', '.wpmi_widget_checkbox', function(event) {
            $(this).parent().next().slideToggle(300);
        });

        $('.wpmi_select2').select2({
            placeholder: wpmi_ajax.select_groups,
            allowClear: true
        });

    });

})(jQuery);
