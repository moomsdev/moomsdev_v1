<?php

add_filter('woocommerce_empty_price_html', static function () {
    return __('<span>Liên hệ</span>', 'mooms');
});

/**
 * Hàm ajax thêm vào giỏ hàng
 *
 * @var int quantity
 * @var int product_id
 * @var int variation_id
 * @var int variation
 */
add_action('wp_ajax_nopriv_gm_add_to_cart', 'ajaxAddToCart');
add_action('wp_ajax_gm_add_to_cart', 'ajaxAddToCart');
function ajaxAddToCart()
{
    ob_start();
    $product_id        = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $product           = wc_get_product($product_id);
    $quantity          = empty($_POST['quantity']) ? 1 : apply_filters('woocommerce_stock_amount', $_POST['quantity']);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    if ($product->is_type('variable')) {
        $variation_id      = $_POST['variation_id'];
        $variation         = $_POST['variation'];
        $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variation);

        try {
            if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation)) {
                do_action('woocommerce_ajax_added_to_cart', $product_id);
                if (get_option('woocommerce_cart_redirect_after_add') === 'yes') {
                    wc_add_to_cart_message($product_id);
                }
                WC_AJAX::get_refreshed_fragments();
            } else {
                wp_send_json_error(apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));
            }
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    } else {
        $quantity = empty($_POST['quantity']) ? 1 : apply_filters('woocommerce_stock_amount', $_POST['quantity']);

        try {
            if ($passed_validation && WC()->cart->add_to_cart($product_id, $quantity)) {
                do_action('woocommerce_ajax_added_to_cart', $product_id);
                if (get_option('woocommerce_cart_redirect_after_add') === 'yes') {
                    wc_add_to_cart_message($product_id);
                }
                WC_AJAX::get_refreshed_fragments();
            } else {
                wp_send_json_error(apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id));
            }
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }
}

function buttonAjaxAddToCart()
{
}

/**
 * @return array|int|\WP_Error
 */
function getRootProductCategories()
{
    return get_terms([
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
        'parent'     => 0,
    ]);
}

/**
 * @param \WP_Term $productCat
 * @param null     $productCount
 *
 * @return \WP_Query
 */
function getProductsByCategory(WP_Term $productCat, $productCount = null)
{
    $productCount = empty($productCount) ? get_option('posts_per_page') : $productCount;
    return new WP_Query([
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'posts_per_page'      => $productCount,
        'tax_query'           => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => $productCat->term_id,
                'operator' => 'IN',
            ],
        ],
    ]);
}

/**
 * @param null $productCount
 *
 * @return \WP_Query
 */
function getFeaturedProducts($productCount = null)
{
    $productCount = empty($productCount) ? get_option('posts_per_page') : $productCount;
    return new WP_Query([
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'posts_per_page'      => $productCount,
        'tax_query'           => [
            [
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            ],
        ],
    ]);
}

/**
 * @param null $productCount
 *
 * @return \WP_Query
 */
function getIsOnSaleProducts($productCount = null)
{
    $productCount = empty($productCount) ? get_option('posts_per_page') : $productCount;
    return new WP_Query([
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
        'posts_per_page'      => $productCount,
        'meta_query'          => [
            'relation' => 'OR',
            [ // Simple products type
                'key'     => '_sale_price',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'numeric',
            ],
            [ // Variable products type
                'key'     => '_min_variation_sale_price',
                'value'   => 0,
                'compare' => '>',
                'type'    => 'numeric',
            ],
        ],
    ]);
}

/**
 * @param null $productCount
 *
 * @return \WP_Query
 */
function getBestSellingProducts($productCount = null)
{
    $productCount = empty($productCount) ? get_option('posts_per_page') : $productCount;
    return new WP_Query([
        'post_type'           => 'product',
        'post_status'         => 'publish',
        'meta_key'            => 'total_sales',
        'orderby'             => 'meta_value_num',
        'ignore_sticky_posts' => true,
        'posts_per_page'      => $productCount,
    ]);

}

function getTopRatingProducts($productCount = null)
{
    $productCount = empty($productCount) ? get_option('posts_per_page') : $productCount;
    return new WP_Query([
        'posts_per_page' => $productCount,
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'meta_key'       => '_wc_average_rating',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
    ]);
}

/**
 * @return int[]
 */
function getProductGalleryImageIds() : array
{
    /**
     * @var \WC_Product $product
     */ global $product;
    return $product->get_gallery_image_ids();
}

function getProductPrice(WC_Product $product)
{
    if ($product->is_type('variable')) {
        return $product->get_variation_price('min');
    }

    $regularPrice = $product->get_regular_price();
    $salePrice    = $product->get_sale_price();
    if (!empty($salePrice)) {
        return $salePrice;
    }

    if (empty($regularPrice)) {
        return 0;
    }

    return $regularPrice;
}

function getProductPercentageSaleOff(WC_Product $product)
{
    if (!$product->is_on_sale()) {
        return 0;
    }
    $max_percentage = 0;
    if ($product->is_type('simple')) {
        $max_percentage = (($product->get_regular_price() - $product->get_sale_price()) / $product->get_regular_price()) * 100;
    } elseif ($product->is_type('variable')) {
        foreach ($product->get_children() as $child_id) {
            $variation = wc_get_product($child_id);
            $price     = $variation->get_regular_price();
            $sale      = $variation->get_sale_price();
            if ($price !== 0 && !empty($sale)) {
                $percentage = ($price - $sale) / $price * 100;
            } else {
                $percentage = 0;
            }

            if ($percentage > $max_percentage) {
                $max_percentage = $percentage;
            }
        }
    }

    return round($max_percentage);
}

/**
 * @param \WP_Term $category
 * @param null     $width
 * @param null     $height
 *
 * @return false|string
 */
function getProductCategoryThumbnail(WP_Term $category, $width = null, $height = null)
{
    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
    return getImageUrlById($thumbnail_id, $width, $height);
}

function theProductPrice()
{
    /**
     * @var \WC_Product $product
     */ global $product;
    $price = getProductPrice($product);
    echo '<span class="product__price">' . (empty($price) ? __('Liên hệ', 'mooms') : wc_price($price)) . '</span>';
}

function theProductPercentageSaleOff()
{
    /**
     * @var \WC_Product $product
     */ global $product;
    $percent = getProductPercentageSaleOff($product);
    if (empty($percent)) {
        echo "<span class=\"product__percent-sale-off\">{$percent}%</span>";
    }
}
