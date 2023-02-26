jQuery(document).ready(function($) {
    console.log(RCT_OBJ.form_id);
    $(document).on("gform_confirmation_loaded", function(e, form_id) {
        console.log(RCT_OBJ.form_id);
        if (form_id == RCT_OBJ.form_id) {
            $('.woocommerce-notices-wrapper, .woocommerce-cart-form').hide();
        }
    });
});