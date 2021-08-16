<?php
/**
 * Register post types.
 *
 * @link https://developer.wordpress.org/reference/functions/register_post_type/
 *
 * @hook    init
 * @package WPEmergeTheme
 */

use Carbon_Fields\Container;
use Carbon_Fields\Field\Field;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


add_action('carbon_fields_register_fields', function () {
    Container::make('post_meta', __('Thông tin thêm', 'mooms'))
             ->set_context('normal')
             ->set_priority('core')
             ->where('post_type', 'IN', ['blog'])
             ->add_fields([
                 Field::make('media_gallery', 'gallery' , __('Album __ 1170x784 ', 'mooms')) ->set_duplicates_allowed( false ),
                 Field::make('complex', 'featured__complex'     , __('Nhập thông tin:', 'mooms'))
                      ->set_layout( 'tabbed-horizontal')
                      ->add_fields([
                          Field::make('media_gallery',   '__album'  , __('Album __ 572x322:' , 'mooms')) ->set_width(20) ->set_duplicates_allowed(false),
                          Field::make('text',            '__title'  , __('Tiêu đề:'          , 'mooms')) ->set_width(30),
                          Field::make('textarea',        '__desc'   , __('Mô tả:'            , 'mooms')) ->set_width(60),
                          Field::make('rich_text',       '__content', __('Nội dung:'         , 'mooms')),
                      ])->set_header_template('<% if (__title) { %><%- __title %><% } %>'),
             ]);
});
