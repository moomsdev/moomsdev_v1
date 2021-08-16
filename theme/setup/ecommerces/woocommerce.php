<?php

// remove some fields from billing form
add_filter('woocommerce_billing_fields', static function ($fields = []) {
    // ref - https://docs.woothemes.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
    unset($fields['billing_company'], $fields['billing_address_2'], $fields['billing_state'], $fields['billing_postcode']);
    // unset($fields['billing_address_1']);
    // unset($fields['billing_city']);
    // unset($fields['billing_phone']);
    // unset($fields['billing_country']);
    return $fields;
});

// Remove some fields from billing form
add_filter('woocommerce_checkout_fields', static function ($fields) {
    // Our hooked in function - $fields is passed via the filter!
    // Get all the fields - https://docs.woothemes.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
    // unset($fields['billing']['billing_company']);
    // unset($fields['billing']['billing_address_1']);
    // unset($fields['billing']['billing_address_2']);
    // unset($fields['billing']['billing_city']);
    // unset($fields['billing']['billing_postcode']);
    // unset($fields['billing']['billing_country']);
    // unset($fields['billing']['billing_state']);
    // unset($fields['billing']['billing_phone']);
    //Removes Additional Info title and Order Notes
    // add_filter( 'woocommerce_enable_order_notes_field', '__return_false',9999 );
    return $fields;
}, 99);

// if you want to add the form-group class around the label and the input
// add form-control to the actual input
add_filter('woocommerce_checkout_fields', static function ($fields) {
    foreach ($fields as &$fieldset) {
        foreach ($fieldset as &$field) {
            $field['class'][] = 'form-group';
            $field['input_class'][] = 'form-control';
        }
    }
    return $fields;
}, 99);

/**
 * Remove product type grouped, external, variable
 */
add_filter('product_type_selector', static function ($types) {
    unset($types['grouped'], $types['external'], $types['variable']);
    return $types;
});

