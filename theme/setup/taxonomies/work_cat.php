<?php

/**
 * Register custom taxonomies.
 *
 * @link https://developer.wordpress.org/reference/functions/register_taxonomy/
 *
 * @hook    init
 * @package WPEmergeTheme
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field\Field;

if (!defined('ABSPATH')) {
    exit;
}

// Custom hierarchical taxonomy (like categories).
// phpcs:disable

add_action('init', function () {
    register_taxonomy(
        'work_cat',
        array('work'),
        array(
            'labels'            => array(
                'name'              => __('Danh mục Works', 'app'),
                'singular_name'     => __('Custom Taxonomy', 'app'),
                'search_items'      => __('Search Custom Taxonomies', 'app'),
                'all_items'         => __('All Custom Taxonomies', 'app'),
                'parent_item'       => __('Danh mục cha', 'app'),
                'parent_item_colon' => __('Parent Custom Taxonomy:', 'app'),
                'view_item'         => __('View Custom Taxonomy', 'app'),
                'edit_item'         => __('Chỉnh sửa', 'app'),
                'update_item'       => __('Cập nhật', 'app'),
                'add_new_item'      => __('Thêm mới', 'app'),
                'new_item_name'     => __('New Custom Taxonomy Name', 'app'),
                'menu_name'         => __('Danh mục Works', 'app'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'taxonomy-work'),
        )
    );
});

// phpcs:enable
