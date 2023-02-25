jQuery(document).ready(function($) {
    $(document).on("gform_confirmation_loaded", function(e, form_id) {
        if(form_id == 3) {
            $('.woocommerce-notices-wrapper, .woocommerce-cart-form').hide();
        }
    });
});