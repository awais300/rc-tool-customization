jQuery(document).ready(function($) {
    $(document).on("gform_confirmation_loaded", function(e, form_id) {
        if (form_id == RCT_OBJ.form_id) {
            $('.woocommerce-notices-wrapper, .woocommerce-cart-form').hide();
        }
    });


});

var PC_IS_SPECIAL_OPTION_OPTED_1 = false;
var PC_IS_SPECIAL_OPTION_OPTED_2 = false;
var PC_IS_SPECIAL_OPTION_OPTED_3 = false;

wp.hooks.addAction('PC.fe.start', 'mkl/product_configurator', function(configurator) {
    console.log('Product configurator started!');

    jQuery(document).ready(function($) {

        if (RCT_OBJ.is_guest_user) {
            guest_change_add_to_cart_text();
        } else {
            setTimeout(function() {
                update_notes($('.sp-option textarea'));
                update_size($('.sp-option input'));
                update_shelf($('.shelf.sp-option li.choices-list li.active button.choice-item'), $);
            }, 2000);
        }

        $(document).on('input', '.sp-option textarea', function(e) {
            e.preventDefault();
            update_notes($(this));
        });

        $(document).on('input', '.sp-option input', function(e) {
            e.preventDefault();
            update_size($(this));
        });

        $(document).on('click', '.shelf.sp-option li.choices-list button.choice-item', function() {
            update_shelf($(this), $);
        });

    });
});


function update_notes($obj) {
    if ($obj.val().trim() !== '') {
        PC_IS_SPECIAL_OPTION_OPTED_1 = true;
        change_add_to_cart_text();
    } else {
        PC_IS_SPECIAL_OPTION_OPTED_1 = false;
        change_add_to_cart_text();
    }
}

function update_size($obj) {
    if ($obj.val().trim() !== '') {
        PC_IS_SPECIAL_OPTION_OPTED_2 = true;
        change_add_to_cart_text();
    } else {
        PC_IS_SPECIAL_OPTION_OPTED_2 = false;
        change_add_to_cart_text();
    }
}

function update_shelf($obj, $) {
    var option_text = $obj.find('span.choice-name').text();

    if (option_text != 'No Shelf' && (option_text == 'Welded-in Shelf' || option_text == 'Adjustable Shelf')) {
        PC_IS_SPECIAL_OPTION_OPTED_3 = true;
        change_add_to_cart_text();
    } else {
        PC_IS_SPECIAL_OPTION_OPTED_3 = false;
        change_add_to_cart_text();
    }
}

function change_add_to_cart_text() {
    if (PC_IS_SPECIAL_OPTION_OPTED_1 || PC_IS_SPECIAL_OPTION_OPTED_2 || PC_IS_SPECIAL_OPTION_OPTED_3) {
        jQuery('button.configurator-add-to-cart span').text('Add to RFQ');
        jQuery('button.edit-cart-item.configurator-add-to-cart span').text('Edit item in RFQ');
    } else {
        jQuery('button.configurator-add-to-cart span').text('Add to Cart');
        jQuery('button.edit-cart-item.configurator-add-to-cart span').text('Edit item in Cart');
    }
}

function guest_change_add_to_cart_text() {
    jQuery('button.configurator-add-to-cart span').text('Add to RFQ');
    jQuery('button.edit-cart-item.configurator-add-to-cart span').text('Edit item in RFQ');
}