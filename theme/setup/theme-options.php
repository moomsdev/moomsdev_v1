<?php

/**
 * Theme Options.
 *
 * Here, you can register Theme Options using the Carbon Fields library.
 *
 * @link    https://carbonfields.net/docs/containers-theme-options/
 *
 * @package WPEmergeCli
 */

use Carbon_Fields\Container\Container;
use Carbon_Fields\Field\Field;

$optionsPage = Container::make('theme_options', __('Themes Option', 'mooms'))
    ->set_page_file('app-theme-options.php')
    ->set_page_menu_position(3)
    ->set_icon('dashicons-admin-tools')
    ->set_page_menu_title(__('Themes Option', 'mooms'))
    ->add_tab(__('Hình ảnh thương hiệu | Branding', 'mooms'), [
        Field::make('image', 'logo_website' .currentLanguage(), __('Logo', 'mooms')),
    ]);
Container::make('theme_options', __('Đầu trang | Header', 'mooms'))
    ->set_page_parent($optionsPage)
    ->set_page_file(__('header', 'mooms'))
    ->add_fields([
        Field::make('image',   'bg_slider_banner' .currentLanguage(), __('Background slider __ 1440x1024', 'mooms')),
        Field::make('complex', 'text_slider' .currentLanguage(), __('Nhập thông tin:', 'mooms'))
            ->set_layout('tabbed-horizontal')
            ->add_fields([
                Field::make('textarea', '__content', __('Nội dung', 'mooms')),
            ]),
    ]);

Container::make('theme_options', __('Thân trang | Index', 'mooms'))
    ->set_page_parent($optionsPage)
    ->set_page_file(__('index', 'mooms'))
    ->add_tab(__('Danh mục dự án | Project Taxonomy', 'mooms'), [
        Field::make('text',   'project__title' .currentLanguage(), __('Tiêu đề:', 'mooms'))->set_width(50),
        Field::make('text',   'project__link'  .currentLanguage(), __('Đường dẫn:', 'mooms'))->set_width(50),
        // Field::make('association', 'project__taxonomy' . currentLanguage(), __('Chọn danh mục dự án:', 'mooms'))
        //     ->set_types([[
        //         'type'     => 'term',
        //         'taxonomy' => 'work_cat',
        //     ]]),
    ])
    ->add_tab(__('Dự án nổi bật | Project Featured', 'mooms'), [
        Field::make('text', 'project_featured__title' . currentLanguage(), __('Tiêu đề:', 'mooms')),
        Field::make('association', 'project_featured' . currentLanguage(), __('Chọn dự án nổi bật:', 'mooms'))
            ->set_types([[
                'type'      => 'post',
                'post_type' => 'work',
            ]]),
    ])
    ->add_tab(__('Gác Sao | About Us', 'mooms'), [
        Field::make('select', 'about_us' . currentLanguage(), __('Chọn trang giới thiệu:', 'mooms'))
            ->set_options(function () {
                return getListAllPages();
            }),
    ])

    ->add_tab(__('Tin tức | Blogs', 'mooms'), [
        Field::make('text', 'blog__title'        . currentLanguage(), __('Tiêu đề:', 'mooms')),
        Field::make('association', 'blog_featured' . currentLanguage(), __('Chọn tin tức nổi bật:', 'mooms'))
             ->set_types([[
                 'type'      => 'post',
                 'post_type' => 'blog',
             ]]),
    ]);

Container::make('theme_options', __('Chân trang | Footer', 'mooms'))
    ->set_page_parent($optionsPage)
    ->set_page_file(__('footer', 'mooms'))
    ->add_tab(__('Thông tin liên hệ', 'mooms'), [
        Field::make('textarea', 'slogan' . currentLanguage(), __('Slogan:',    'mooms')),
        Field::make('text', 'address' . currentLanguage(), __('Email', 'mooms'))                  ->set_default_value('22/4 Le Dinh Duong Street, Hai Chau District, Da Nang City, Vietnam.'),
        Field::make('text', 'phone_1' . currentLanguage(), __('Hotline (gọi trực tiếp)', 'mooms'))->set_width(50) ->set_default_value('0369864494'),
        Field::make('text', 'phone_2' . currentLanguage(), __('Hotline (Hiển thị)', 'mooms'))     ->set_width(50) ->set_default_value('(+84) 369 864 494'),
        Field::make('text', 'email'   . currentLanguage(), __('Email', 'mooms'))                  ->set_default_value('gacsao.branding@gmail.com'),
        Field::make('complex', 'socials' . currentLanguage(), __('Mạng xã hội:', 'mooms'))        ->set_layout('tabbed-vertical')
         ->add_fields([
             Field::make('text', '__name', __('Tên mạng xã hội', 'mooms')),
             Field::make('text', '__link', __('Đường dẫn', 'mooms')),
         ])->set_header_template('<% if (__name) { %><%- __name %><% } %>'),
    ]);

Container::make('theme_options', __('Insert script', 'app'))
    ->set_page_parent($optionsPage)
    ->set_page_file(__('insert-script', 'mooms'))
    ->add_fields([
        Field::make('text', 'crb_google_maps_api_key', __('Google Maps API Key', 'mooms')),
        Field::make('header_scripts', 'crb_header_script', __('Header Script', 'mooms')),
        Field::make('footer_scripts', 'crb_footer_script', __('Footer Script', 'mooms')),
    ]);

/**
 * Custom Page ABOUT US.
 **/
Container::make('post_meta', __('Trang Giới thiệu', 'mooms'))
    ->set_context('normal') // normal, advanced, side or carbon_fields_after_title
    ->where('post_type', '=', 'page')
    ->where('post_template', '=', 'page_templates/about_us_template.php')
    ->add_tab(__('Giới thiệu | Introduction', 'mooms'), [
        Field::make('image', 'icon', __('Icon', 'mooms')) ->set_width(20),
        Field::make('textarea', 'slogan',  __('Slogan:', 'mooms'))->set_width(80),
        Field::make('textarea', 'content', __('Nội dung giới thiệu:', 'mooms')),
        Field::make('image', 'img_bg', __('Hình ảnh __ 1198x716', 'mooms')),
    ])
    ->add_tab(__('Câu chuyện về Gác Sao | Our Story', 'mooms'), [
        Field::make('text', 'out_story_title',  __('Tiêu đề', 'mooms')) ->set_default_value('Out Story'),
        Field::make('textarea', 'out_story_content_1', __('', 'mooms')),
        Field::make('textarea', 'out_story_content_2', __('', 'mooms')),
    ])
    ->add_tab(__('Khách hàng Gác Sao | Client', 'mooms'), [
        Field::make('text', 'client__title', __('Tiêu đề:', 'mooms'))->set_default_value('Sellected Clients'),
        Field::make('media_gallery', 'client_logo', __('Chọn logo __ 80x80:', 'mooms')),
    ])
    ->add_tab(__('Đối tác Gác Sao | Partner', 'mooms'), [
        Field::make('text', 'partner__title', __('Tiêu đề:', 'mooms'))->set_width(30) ->set_default_value('Our Partner'),
        Field::make('textarea', 'partner__desc', __('Mô tả ngắn:', 'mooms'))->set_width(70),
        Field::make('complex', 'partner', __('Đối tác:', 'mooms')) ->set_layout('tabbed-vertical')
         ->add_fields([
             Field::make('image', '__logo', __('Logo __ 195x128', 'mooms'))->set_width(25),
             Field::make('text', '__name', __('Tên đối tác', 'mooms'))->set_width(65),
             Field::make('text', '__website', __('Website', 'mooms')),
         ])->set_header_template('<% if (__name) { %><%- __name %><% } %>'),
    ])
    ->add_tab(__('Suy nghĩ của Gác Sao | Our thinking', 'mooms'), [
        Field::make('text', 'our_thinking_title', __('Tiêu đề:', 'mooms'))->set_default_value('Our thinking'),
        Field::make('textarea', 'our_thinking_content_1', __('', 'mooms')),
        Field::make('textarea', 'our_thinking_content_2', __('', 'mooms')),
        Field::make('complex', 'our_thinking', __('Tiêu chuẩn thiết kế: ', 'mooms')) ->set_layout('tabbed-vertical')
         ->add_fields([
             Field::make('text', '__title', __('Tiêu đề', 'mooms')),
             Field::make('textarea', '__content', __('Nội dung', 'mooms')),
         ])->set_header_template('<% if (__title) { %><%- __title %><% } %>'),
    ])
    ->add_tab(__('Gác Sao Studio | Our Studio', 'mooms'), [
        Field::make('text', 'our_studio_title', __('Tiêu đề:', 'mooms'))->set_width(30) ->set_default_value('Our Studio'),
        Field::make('textarea', 'our_studio_desc', __('Mô tả ngắn:', 'mooms'))->set_width(70),
        Field::make('media_gallery', 'our_studio_album', __('Album Studio', 'mooms')),
    ]);
