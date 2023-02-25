jQuery(document).ready(function($) {
    $(document).on('click', 'ul.product_data_tabs.wc-tabs li', function(e) {
        e.preventDefault();
        if ($(this).hasClass('rctool_custom_tab_data_options') && $(this).hasClass('active')) {
            $('.rctool_custom_tab_section').css('display', 'block');
        } else {
            $('.rctool_custom_tab_section').css('display', 'none');
        }
    });
});