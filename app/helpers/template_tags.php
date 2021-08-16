<?php

/**
 * get resources image uri
 *
 * @param string $path
 *
 * @return string
 */
function getImageAsset($path) {
    $my_theme   = wp_get_theme();
    $theme_name = str_replace('/theme', '', $my_theme->get_stylesheet());
    if (carbon_get_theme_option('use_short_url') !== true) {
        $siteUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/wp-content/themes/$theme_name/resources/images/";
    } else {
        $siteUrl = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . "/img/";
    }

    return $siteUrl . $path;
}

function template($name) {
    get_template_part('templates/' . $name);
}

function getOption($name) {
    return carbon_get_theme_option($name . currentLanguage());
}

/**
 * Get theme option dạng image
 *
 * @param string $name
 * @param int    $w
 * @param int    $h
 *
 * @return false|string
 */
function getOptionImageUrl($name, $w, $h) {
    return getImageUrlById(getOption($name), $w, $h);
}

function getPostThumbnailUrl($postId, $width = null, $height = null) {
    $defaultImage = getImageUrlById(getOption('hinh_anh_mac_dinh'), $width, $height);
    try {
        $imageId = get_post_thumbnail_id($postId);
        if (empty($imageId)) {
            return $defaultImage;
        }

        if ($width === null && $height === null) {
            return wp_get_attachment_image_url($imageId, 'full');
        }

        return getImageUrlById($imageId, $width, $height);
    } catch (\Exception $ex) {
        return $defaultImage;
    }
}

function getExcerpt($postId, $limit) {
    return subString(get_the_excerpt($postId), $limit);
}

function updateViewCount($postId = null) {
    $postId = empty($postId) ? get_the_ID() : $postId;

    $count_key = '_gm_view_count';
    $count     = (int)get_post_meta($postId, $count_key, true);
    if (empty($count)) {
        $count = 1;
        delete_post_meta($postId, $count_key);
        add_post_meta($postId, $count_key, $count);
    } else {
        $count++;
        update_post_meta($postId, $count_key, $count);
    }

    return $count;
}

function getViewCount($postId = null) {
    $postId    = empty($postId) ? get_the_ID() : $postId;
    $count_key = '_gm_view_count';
    $count     = get_post_meta($postId, $count_key, true);
    if (empty($count)) {
        $count = 0;
        delete_post_meta($postId, $count_key);
        add_post_meta($postId, $count_key, $count);
    }

    return $count;
}

function getPostMeta($name, $id = null) {
    $id = empty($id) ? get_the_ID() : $id;
    return carbon_get_post_meta($id, $name);
}

function getPostMetaImageUrl($name, $id = null, $w = null, $h = null) {
    $id = empty($id) ? get_the_ID() : $id;
    return getImageUrlById(carbon_get_post_meta($id, $name), $w, $h);
}

function thePostMeta($name) {
    echo getPostMeta($name, get_the_ID());
}

function thePostMetaImageUrl($name = '', $w = null, $h = null) {
    echo getPostMetaImageUrl($name, get_the_ID(), $w, $h);
}

/**
 * Echo view count of post
 *
 * @param null $postId
 */
function theViewCount($postId = null) {
    echo getViewCount($postId);
}

function thePostThumbnailUrl($width = null, $height = null) {
    echo getPostThumbnailUrl(get_the_ID(), $width, $height);
}

function theTitle($limit = 999) {
    echo subString(get_the_title(), $limit);
}

function theExcerpt($limit = 9999) {
    echo '<p>' . getExcerpt(get_the_ID(), $limit) . '</p>';
}

function theContent() {
    $content = get_the_content();
    $content = apply_filters('the_content', $content);
    $content = str_replace(']]>', ']]&gt;', $content);
    echo !empty($content) ? $content : __('Dữ liệu đang được cập nhật', 'mooms');
}

function theTime() {
    the_time(get_option('date_format') . ' ' . get_option('time_format'));
}

function theDate() {
    the_time(get_option('date_format'));
}

function theOption($name) {
    echo getOption($name);
}

function theOptionImage($name, $width = null, $height = null) {
    echo getImageUrlById(getOption($name), $width, $height);
}

/**
 * Load resource
 *
 * @param string $path
 */
function theAsset($path) {
    echo theImageAsset($path);
}

/**
 * Tạo phân trang sử dụnng boostrap 4
 *
 * @param mixed|\WP_Query $query
 */
function thePagination($query = null) {
    if (empty($query)) {
        global $wp_query;
        $query = $wp_query;
    }

    $paged = (get_query_var('paged') === 0) ? 1 : get_query_var('paged');
    $pages = paginate_links([
        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $query->max_num_pages,
        'type'      => 'array',
        'prev_next' => true,
    ]);
    if (is_array($pages)) {
        $pagination = '<div class="pagination" data-aos="fade-up" data-aos-duration="1500"><ul class="pagination">';
        foreach ($pages as $page) {
            $pagination .= '<li class="page-item"> ' . str_replace('page-numbers', 'page-link', $page) . '</li>';
        }
        $pagination .= '</ul></div>';
        echo $pagination;
    }
}

/**
 * Tạo hộp chia sẻ
 */
function theShareBox() {
    get_template_part('part_templates/share_box');
}

/**
 * Load bản đồ Google
 *
 * @param $name
 * @param $height
 */
function theGoogleMap($idElement, $name, $height = 350) {
    $location = getOption($name); ?>

    <div id="<?php echo $idElement ?>" style="width:100%;height:<?php echo $height ?>px" data-lat="<?php echo $location['lat'] ?>" data-lng="<?php echo $location['lng'] ?>" data-zoom="<?php echo $location['zoom'] ?>"></div>
    <script>
        setTimeout(() => {
            let mapElement = document.getElementById('<?php echo $idElement ?>');
            if (mapElement) {
                let myLatLng = {
                    lat: parseFloat(mapElement.getAttribute("data-lat")),
                    lng: parseFloat(mapElement.getAttribute("data-lng"))
                };
                let map = new google.maps.Map(mapElement, {
                    center: myLatLng,
                    zoom  : parseInt(mapElement.getAttribute("data-zoom"))
                });
                let marker = new google.maps.Marker({
                    position: myLatLng,
                    map     : map,
                });
            }
        }, 200);
    </script>
<?php }

/**
 * Tạo breadcrumb
 */
function theBreadcrumb() {
    get_template_part('part_templates/breadcrumb');
}

function thePhoneNumberFixedButton() {
    get_template_part('part_templates/hotline_button');
}

function theFanPageFixedButton() {
    ?>
    <script type="text/javascript">
        (function () {
            var options = {
                facebook        : "<?php theOption('fan_page_id') ?>", // Facebook page ID
                company_logo_url: "<?php theOption('desktop_logo') ?>", // URL of company logo (png, jpg, gif)
                greeting_message: "Chào bạn, Chúng tôi có thể giúp được gì cho bạn ?", // Text of greeting message
                call_to_action  : "Nhắn tin", // Call to action
                position        : "right", // Position may be 'right' or 'left'
            };
            var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
            var s = document.createElement('script');
            s.type = 'text/javascript';
            s.async = true;
            s.src = url + '/widget-send-button/js/init.js';
            s.onload = function () {
                WhWidgetSendButton.init(host, proto, options);
            };
            var x = document.getElementsByTagName('script')[0];
            x.parentNode.insertBefore(s, x);
        })();
    </script>
    <?php
}

function theCopyright() {
    $date = date('Y');
    $name = get_bloginfo('name');
    echo "Bản quyền @ $date thuộc về <a href='/'>$name</a>, tất cả các quyền được bảo hộ. Thiết kế website bởi <a href='" . AUTHOR['website'] . "' target='_blank'>" . AUTHOR['name'] . '</a>';
}

function thePageTitle() {
    echo getPageTitle();
}

function getPageTitle() {
    $obj   = get_queried_object();
    $title = get_bloginfo('name');
    if (is_single() || is_page()) {
        $title = get_the_title();
    } elseif (is_search()) {
        /* translators: search results page title */
        $title = sprintf(__('Kết quả tìm kiếm cho từ khóa: %s', 'mooms'), get_search_query());
    } elseif (is_category()) {
        /* translators: category post listing page title */
        $title = single_cat_title('', false);
    } elseif (is_tag()) {
        /* translators: tag post listing page title */
        $title = sprintf(__('Tag: %s', 'mooms'), single_tag_title('', false));
    } elseif (is_day()) {
        /* translators: day archive post listing page title */
        $title = sprintf(__('Daily Archives: %s', 'mooms'), get_the_time('F jS, Y'));
    } elseif (is_month()) {
        /* translators: month archive post listing page title */
        $title = sprintf(__('Monthly Archives: %s', 'mooms'), get_the_time('F, Y'));
    } elseif (is_year()) {
        /* translators: year archive post listing page title */
        $title = sprintf(__('Yearly Archives: %s', 'mooms'), get_the_time('Y'));
    } elseif (is_author()) {
        /* translators: author archive post listing page title */
        $title = sprintf(__('Posts by %s', 'mooms'), get_the_author());
    } elseif (class_exists('WooCommerce') && is_woocommerce()) {
        $title = woocommerce_page_title(false);
    } elseif (is_archive()) {
        if ($obj instanceof WP_Term) {
            $title = $obj->name;
        } elseif ($obj instanceof WP_Post_Type) {
            $title = $obj->label;
        }
    } elseif (is_404()) {
        $title = __('Lỗi 404 - Không tìm thấy trang bạn yêu cầu', 'mooms');
    }
    return $title;
}

function theLanguageSwitcher($showName = true, $showFlag = false) {
    if (function_exists('pll_the_languages')) {
        echo '<ul class="language-switcher">';
        pll_the_languages([
            'show_names'    => $showName,
            'show_flags'    => $showFlag,
            'hide_if_empty' => false,
        ]);
        echo '</ul>';
    }
}
