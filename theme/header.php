<?php

/**
 * Theme header partial.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package WPEmergeTheme
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0" />

    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php app_shim_wp_body_open(); ?>
    <div class="wrapper_hda">
        <header id="header" style="background-image: url('<?php echo getOptionImageUrl('bg_slider_banner',1440,1024); ?>')">
            <div class="top_header">
                <div class="container">
                    <div class="top_header__inner">
                        <section class="language"><?php theLanguageSwitcher() ?></section>
                        <section class="logo">
                            <a href="<?php bloginfo('url') ?>">
                                <img src="<?php theOptionImage('logo_website',78,80); ?>" alt="<?php bloginfo('url') ?>">
                            </a>
                        </section>
                        <section class="main_menu" id="main_menu">
                            <div id="menu"> <span><?php echo __('Discover','mooms') ?></span></div>
                            <?php
                            wp_nav_menu([
                                'menu'           => 'main-menu',
                                'theme_location' => 'main-menu',
                                'container'      => 'ul',
                                'menu_class'     => '',
                                'walker'         => new STC_Menu_Walker(),
                            ])
                            ?>

                        </section>
                    </div>
                </div>
            </div>
            <div class="inner_header">
                <div class="swiper-container __text_slider">
                    <div class="swiper-wrapper">
                        <?php
                        $text_slider= getOption('text_slider');
                        $i= 0;
                        foreach ($text_slider as $slider) {
                            ?>
                            <div class="swiper-slide">
                                <div class="text_slider">
                                    <?php echo apply_filters('the_content',$slider['__content']) ?>
                                </div>
                            </div>
                        <?php } ?>

                    </div>

                </div>
            </div>
        </header>
