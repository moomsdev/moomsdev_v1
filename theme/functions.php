<?php

/**
 * Bootstrap theme.
 *
 * The purpose of this file is to bootstrap your theme by loading all dependencies and helpers.
 *
 * YOU SHOULD NORMALLY NOT NEED TO ADD ANYTHING HERE - any custom functionality unreleated
 * to boostrapping the theme should go into a separate helper file.
 * (refer to the directory structure in README.md)
 *
 * @package WPEmergeTheme
 */

use WPEmerge\Facades\WPEmerge;
use WPEmergeTheme\Facades\Theme;

if (!defined('ABSPATH')) {
    exit;
}
define('ALLOW_UNFILTERED_UPLOADS', true);
define('AUTHOR', [
    'name'           => 'Blog Du lịch - Website MOOMS.DEV',
    'email'          => 'mooms.dev@gmail.com',
    'phone_number'   => '0989.64.67.66',
    'logo_url'       => '//mooms.dev/logo.png',
    'logo_color_url' => '//mooms.dev/logo_color.png',
    'logo_black_url' => '//mooms.dev/logo_black.png',
    'logo_white_url' => '//mooms.dev/logo.png',
    'favicon'        => '//mooms.dev/favicon.png',
    'website'        => '//mooms.dev',
    'date_started'   => get_option('_theme_info_date_started'),
    'date_published' => get_option('_theme_info_date_publish'),
]);
define('SUPER_USER', ['mooms_dev']);
/**
 * Constant definitions.
 */
define('APP_APP_DIR_NAME', 'app');
define('APP_APP_HELPERS_DIR_NAME', 'helpers');
define('APP_APP_ROUTES_DIR_NAME', 'routes');
define('APP_APP_SETUP_DIR_NAME', 'setup');
define('APP_DIST_DIR_NAME', 'dist');
define('APP_RESOURCES_DIR_NAME', 'resources');
define('APP_THEME_DIR_NAME', 'theme');
define('APP_VENDOR_DIR_NAME', 'vendor');
define('APP_THEME_USER_NAME', 'users');
define('APP_THEME_ECOMMERCE_NAME', 'users');
define('APP_THEME_POST_TYPE_NAME', 'post-types');
define('APP_THEME_TAXONOMY_NAME', 'taxonomies');
define('APP_THEME_WIDGET_NAME', 'widgets');
define('APP_THEME_BLOCK_NAME', 'blocks');
define('APP_THEME_WALKER_NAME', 'walkers');
define('APP_DIR', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('APP_APP_DIR', APP_DIR . APP_APP_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_HELPERS_DIR', APP_APP_DIR . APP_APP_HELPERS_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_ROUTES_DIR', APP_APP_DIR . APP_APP_ROUTES_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_RESOURCES_DIR', APP_DIR . APP_RESOURCES_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_THEME_DIR', APP_DIR . APP_THEME_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_VENDOR_DIR', APP_DIR . APP_VENDOR_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_DIST_DIR', APP_DIR . APP_DIST_DIR_NAME . DIRECTORY_SEPARATOR);
// define('APP_APP_SETUP_DIR', APP_APP_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_ECOMMERCE_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_ECOMMERCE_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_USER_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_USER_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_POST_TYPE_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_POST_TYPE_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_TAXONOMY_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_TAXONOMY_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_WIDGET_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_WIDGET_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_BLOCK_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_BLOCK_NAME . DIRECTORY_SEPARATOR);
define('APP_APP_SETUP_WALKER_DIR', APP_THEME_DIR . APP_APP_SETUP_DIR_NAME . DIRECTORY_SEPARATOR . APP_THEME_WALKER_NAME . DIRECTORY_SEPARATOR);
/**
 * Load composer dependencies.
 */
if (file_exists(APP_VENDOR_DIR . 'autoload.php')) {
    require_once APP_VENDOR_DIR . 'autoload.php';
    \Carbon_Fields\Carbon_Fields::boot();
}
/**
 * Enable the global Theme:: shortcut so we don't have to type WPEmergeTheme:: every time.
 */
WPEmerge::alias('Theme', \WPEmergeTheme\Facades\Theme::class);
/**
 * Load helpers.
 */
require_once APP_APP_DIR . 'helpers.php';
/**
 * Bootstrap Theme after all dependencies and helpers are loaded.
 */
Theme::bootstrap(require APP_APP_DIR . 'config.php');
/**
 * Register hooks.
 */
require_once APP_APP_DIR . 'hooks.php';
add_action('after_setup_theme', function () {
    /**
     * Load textdomain.
     */
    load_theme_textdomain('mooms', APP_DIR . 'languages');
    /**
     * Register theme support.
     */
    require_once APP_APP_SETUP_DIR . 'theme-support.php';
    /**
     * Register menu locations.
     */
    require_once APP_APP_SETUP_DIR . 'menus.php';
    /**
     * Support ajax functions
     */
    require_once APP_APP_SETUP_DIR . 'ajax.php';
    /**
     * Define guternberg block
     */
    require_once APP_APP_SETUP_DIR . 'gutenberg_blocks.php';
});
// Remove emoji
add_action('init', static function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    /**
     * Filter function used to remove the tinymce emoji plugin.
     *
     * @param array $plugins
     *
     * @return array Difference betwen the two arrays
     */
    add_filter('tiny_mce_plugins', static function ($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, ['wpemoji']);
        } else {
            return [];
        }
    });
    /**
     * Remove emoji CDN hostname from DNS prefetching hints.
     *
     * @param array  $urls          URLs to print for resource hints.
     * @param string $relation_type The relation type the URLs are printed for.
     *
     * @return array Difference betwen the two arrays.
     */
    add_filter('wp_resource_hints', static function ($urls, $relation_type) {
        if ('dns-prefetch' === $relation_type) {
            /** This filter is documented in wp-includes/formatting.php */
            $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');

            $urls = array_diff($urls, [$emoji_svg_url]);
        }
        return $urls;
    }, 10, 2);
});

/**
 * Autoload all required file
 */
$folders = [
    APP_APP_SETUP_ECOMMERCE_DIR,
    APP_APP_SETUP_POST_TYPE_DIR,
    APP_APP_SETUP_POST_TYPE_DIR,
    APP_APP_SETUP_TAXONOMY_DIR,
    APP_APP_SETUP_WALKER_DIR,
];
foreach ($folders as $folder) {
    $filesPath = scandir($folder);
    if ($filesPath !== false) {
        foreach ($filesPath as $item) {
            $file = $folder . $item;
            if (is_file($file)) {
                require_once $folder . $item;
            }
        }
    }
}
add_action('widgets_init', function () {
    /**
     * Register widgets.
     */
    // require_once APP_APP_SETUP_DIR . 'widgets.php';
    $folder = scandir(APP_APP_SETUP_WIDGET_DIR);
    if ($folder !== false) {
        foreach ($folder as $item) {
            $file = APP_APP_SETUP_WIDGET_DIR . $item;
            if (is_file($file)) {
                require_once APP_APP_SETUP_WIDGET_DIR . $item;
            }
        }
    }

    /**
     * Register sidebars.
     */
    require_once APP_APP_SETUP_DIR . 'sidebars.php';
});
add_filter('style_loader_tag', function ($html, $handle) {
    return str_replace("media='all' />", 'media="all" rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">', $html);
    //    return $html;
}, 10, 2);
//===================================================================
////=====================       HA DUY AN       =====================
//===================================================================
new \App\PostTypes\Work();
new \App\PostTypes\Blog();
new \App\PostTypes\Service();



