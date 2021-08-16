<?php

use Carbon\Carbon;
use Intervention\Image\ImageManagerStatic as Image;
use Jenssegers\Agent\Agent;

if (!function_exists('insertArrayAtPosition')) {
	function insertArrayAtPosition($array, $insert, $position)
	{
		return array_slice($array, 0, $position, true) + $insert + array_slice($array, $position, null, true);
	}
}


function get_icl_language_code()
{
	return !defined('ICL_LANGUAGE_CODE') ? '' : ICL_LANGUAGE_CODE;
}

function currentLanguage()
{
	return get_icl_language_code();
}

function adminAsset($path) { return get_stylesheet_directory_uri() . '/../resources/admin/' . $path; }

function getListAllCategories()
{
	$args = [
		'hide_empty' => false,
		'taxonomy'   => 'category',
	];

	$term_query = new WP_Term_Query();


	if (is_admin()) {
		add_filter('terms_clauses', 'filter_terms_by_polylang');
	}

	$terms = $term_query->query($args);

	if (is_admin()) {
		remove_filter('terms_clauses', 'filter_terms_by_polylang');
	}

	$list = [];
	foreach ($terms as $term) {
		$list[$term->term_id] = $term->name;
	}

	return $list;
}

function filter_terms_by_polylang($compact)
{
	//    global $wpdb;
	//    $query        = "select * from " . $wpdb->terms . " where slug = 'pll_" . pll_current_language() . "'";
	//    $translateTax = $wpdb->get_results($query);
	//    if (count($translateTax) > 0) {
	//        $currentLangId = $translateTax[0]->term_id;
	//        $compact['join']  .= " INNER JOIN $wpdb->term_relationships AS pll_tr ON pll_tr.object_id = t.term_id ";
	//        $compact['where'] .= " AND pll_tr.term_taxonomy_id IN ($currentLangId) ";
	//    }
	//
	//    return $compact;
}

function getListAllPages()
{
	$pages = get_posts([
		'post_type'      => 'page',
		'posts_per_page' => -1,
		'lang'           => get_icl_language_code(),
	]);

	$list = [];
	foreach ($pages as $page) {
		$list[$page->ID] = $page->post_title;
	}

	return $list;
}

/**
 * function update_post_meta by mooms
 *
 * @param        $post_id
 * @param        $field_name
 * @param string $value
 *
 * @return bool|false|int
 */
function updatePostMeta($post_id, $field_name, $value = '')
{
	if (empty($value)) {
		return delete_post_meta($post_id, $field_name);
	}

	if (!get_post_meta($post_id, $field_name)) {
		return add_post_meta($post_id, $field_name, $value);
	}

	return update_post_meta($post_id, $field_name, $value);
}

/**
 * Hàm updateUserMeta
 *
 * @param $idUser
 * @param $key
 * @param $value
 *
 * @return bool|false|int
 */
function updateUserMeta($idUser, $key, $value)
{
	if (empty($value)) {
		return delete_user_meta($idUser, $key);
	}

	if (!get_user_meta($idUser, $key)) {
		return add_user_meta($idUser, $key, $value);
	}

	return update_user_meta($idUser, $key, $value);
}

function updateAttachmentSize($attachment_id, $fileName, $width, $height, $type)
{
	$metadata = wp_get_attachment_metadata($attachment_id);
	if (is_array($metadata) && array_key_exists('sizes', $metadata)) {
		$size     = $metadata['sizes'];
		$sizeName = $width . 'x' . $height;
		if (!array_key_exists($sizeName, $size)) {
			$metadata['sizes'][$sizeName] = [
				'file'      => $fileName,
				'width'     => $width,
				'height'    => $height,
				'mime-type' => $type,
			];
		}
		wp_update_attachment_metadata($attachment_id, $metadata);
	}
}

function resizeImage($srcPath, $destinationPath, $maxWidth, $maxHeight, $type = 'webp')
{
	try {
		if (carbon_get_theme_option('use_php_image_magick') === 'yes') {
			Image::configure(['driver' => 'imagick']);
		}
		$image = Image::make($srcPath);
		if ($maxWidth !== 0 || $maxHeight !== 0) {
			$image->fit($maxWidth, $maxHeight, static function ($constraint) {
				$constraint->upsize();
			});
		}
		$image->encode($type);
		$image->save($destinationPath, 85);
	} catch (\Exception $ex) {
		dump($ex);
	}
}

/**
 * Normalizes a path's slashes according to the current OS
 * This solves mixed slashes that are sometimes returned by core functions
 *
 * @param string $path
 *
 * @return string
 */
function crb_normalize_path($path)
{
	return preg_replace('~[/' . preg_quote('\\', '~') . ']~', DIRECTORY_SEPARATOR, $path);
}

/**
 * Truncates a string to a certain word count.
 *
 * @param string  $input       Text to be shortalized. Any HTML will be stripped.
 * @param integer $words_limit number of words to return
 * @param string  $end         the suffix of the shortalized text
 *
 * @return string
 */
function crb_shortalize($input, $words_limit = 15, $end = '...')
{
	return wp_trim_words($input, $words_limit, $end);
}

function subString($str, $limit)
{
	$content = explode(' ', $str, $limit);
	if (count($content) >= $limit) {
		array_pop($content);
		$content = implode(' ', $content) . '...';
	} else {
		$content = implode(' ', $content);
	}

	return preg_replace('`[[^]]*]`', '', $content);
}

/**
 * Load css files for theme
 *
 * @param array $files
 */
function loadStyles($files = [])
{
	add_action('wp_enqueue_scripts', static function () use ($files) {
//		wp_enqueue_style('mooms-css-jquery-ui', asset('plugins/jquery-ui/jquery-ui.min.css'), [], '0.1.0');
//		wp_enqueue_style('mooms-css-util', asset('css/util.css'), [], '0.1.0');
		$count = 1;
		foreach ($files as $file) {
			wp_enqueue_style('mooms-css-' . $count, $file, [], '0.1.0');
			$count++;
		}
//		wp_enqueue_style('mooms-css-theme', asset('css/theme.css'), [], '0.1.0');
		wp_enqueue_style('mooms-css-style', get_stylesheet_directory_uri() . '/style.css', [], '0.1.0');
	}, 1);
}

/**
 * load javascript files for theme
 *
 * @param array $files
 */
function loadScripts($files = [])
{
	add_action('wp_enqueue_scripts', static function () use ($files) {
		// wp_enqueue_script('mooms-js-google-map', 'https://maps.googleapis.com/maps/api/js?key=' . apply_filters('carbon_fields_map_field_api_key', true) . '&libraries=geometry,places,drawing', [], '0.1.0', true);
//		wp_enqueue_script('mooms-js-jquery', asset('js/jquery-3.4.1.min.js'), [], '0.1.0', true);
//		wp_enqueue_script('mooms-js-lazyload', asset('js/lazysizes-umd.min.js'), [], '0.1.0', true);
//		wp_enqueue_script('mooms-js-jquery-validate', asset('plugins/jquery-validation/dist/jquery.validate.min.js'), [], '0.1.0', true);
//		wp_enqueue_script('mooms-js-jquery-validate-method', asset('plugins/jquery-validation/dist/additional-methods.min.js'), [], '0.1.0', true);
//		wp_enqueue_script('mooms-js-jquery-ui', asset('plugins/jquery-ui/jquery-ui.min.js'), [], '0.1.0', true);
		$count = 1;
		foreach ($files as $file) {
			$scriptHandle = 'mooms-js-' . $count;
			wp_enqueue_script($scriptHandle, $file, [], '0.1.0', true);
			$count++;
		}
		// wp_enqueue_script('mooms-js', get_stylesheet_directory_uri() . '/framework/assets/js/mooms.js', [], '0.1.0', true);
//		wp_enqueue_script('theme-js', asset('js/theme.js'), [], '0.1.0', true);
	}, 1);
}

/**
 * Get relate posts
 *
 * @param integer $postId
 * @param integer $postCount
 *
 * @return \WP_Query
 */
function getRelatePosts($postId = null, $postCount = null)
{
	if ($postCount === null) {
		$postCount = get_option('posts_per_page');
	}

	if ($postId === null) {
		global $post;
		$thisPost = $post;
	} else {
		$thisPost = get_post($postId);
	}

	$taxonomies  = get_post_taxonomies($postId);
	$arrTaxQuery = ['relation' => 'OR'];
	foreach ($taxonomies as $taxonomy) {
		$arrTerm = [];
		$terms   = get_the_terms($postId, $taxonomy);
		if (!empty($terms)) {
			foreach ($terms as $term) {
				$arrTerm[] = $term->term_id;
			}
			$arrTaxItem    = [
				'taxonomy'   => $taxonomy,
				'field_name' => 'term_id',
				'operator'   => 'IN',
				'terms'      => $arrTerm,
			];
			$arrTaxQuery[] = $arrTaxItem;
		}
	}

	$posts = new \WP_Query([
		'post_type'      => $thisPost->post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $postCount,
		'post__not_in'   => [$postId],
		'tax_query'      => $arrTaxQuery,
	]);

	return $posts;
}

/**
 * Get latest posts
 *
 * @param string $postType
 * @param int    $postCount
 *
 * @return \WP_Query
 */
function getLatestPosts($postType = 'post', $postCount = null)
{
	if ($postCount === null) {
		$postCount = get_option('posts_per_page');
	}

	return new \WP_Query([
		'post_type'      => $postType,
		'post_status'    => 'publish',
		'posts_per_page' => $postCount,
		'orderBy'        => 'date',
		'order'          => 'desc',
	]);
}

/**
 * Get posts order by view count
 *
 * @param string $postType
 * @param int    $postCount
 *
 * @return \WP_Query
 */
function getTopViewPosts($postType = 'post', $postCount = null)
{
	if ($postCount === null) {
		$postCount = get_option('posts_per_page');
	}

	return new \WP_Query([
		'post_type'      => $postType,
		'post_status'    => 'publish',
		'posts_per_page' => $postCount,
		'meta_key'       => '_gm_view_count',
		'orderby'        => 'meta_value_num',
		'order'          => 'DESC',
	]);
}

/**
 * get random string not include special character
 *
 * @param int $length
 *
 * @return string
 */
function getRandomString($length = 65)
{
	$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString     = '';
	for ($i = 0; $i < $length; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}

	return $randomString;
}

/**
 * Get format human time like facebook
 *
 * @param string $time
 *
 * @return string
 */
function formatHumanTime($time = '2000-12-31 00:00:00')
{
	$seconds = Carbon::now()->diffInSeconds(Carbon::createFromFormat('Y-m-d H:i:s', $time));
	if ($seconds <= 60) {
		return __('Vừa mới đây', 'mooms');
	}

	$minutes = round($seconds / 60);           // value 60 is seconds
	if ($minutes <= 60) {
		if ($minutes < 2) {
			return __('Khoảng 1 phút', 'mooms');
		}

		return $minutes . ' ' . __('phút trước', 'mooms');
	}

	$hours = round($seconds / 3600);           //value 3600 is 60 minutes * 60 sec
	if ($hours <= 24) {
		if ($hours < 2) {
			return __('Khoảng 1 giờ', 'mooms');
		}

		return $hours . ' ' . __('giờ trước', 'mooms');
	}

	$days = round($seconds / 86400);          //86400 = 24 * 60 * 60;
	if ($days <= 7) {
		if ($days < 2) {
			return __('Hôm qua', 'mooms');
		}

		return $hours . ' ' . __('Ngày trước', 'mooms');
	}

	$weeks = round($seconds / 604800);          // 7*24*60*60;
	if ($weeks <= 4.3) {  //4.3 == 52/12
		if ($weeks < 2) {
			return __('Tuần trước', 'mooms');
		}

		return $weeks . ' ' . __('Tuần trước', 'mooms');
	}

	$months = round($seconds / 2629440);     //((365+365+365+365+366)/5/12)*24*60*60
	if ($months <= 12) {
		if ($months < 2) {
			return __('Tháng trước', 'mooms');
		}

		return $weeks . ' ' . __('Tháng trước', 'mooms');
	}

	$years = round($seconds / 31553280);     //(365+365+365+365+366)/5 * 24 * 60 * 60
	if ($years < 1) {
		return __('Năm ngoái', 'mooms');
	}

	return $years . ' ' . __('Năm trước', 'mooms');
}

/**
 * Get and resize image url by attachment id without add_image_size
 *
 * @param int  $attachment_id
 * @param null $width
 * @param null $height
 *
 * @return false|string
 */
function getImageUrlById($attachment_id, $width = null, $height = null)
{
	// if ($width === null && $height === null) {
	// 	return wp_get_attachment_image_url($attachment_id, 'full');
	// }

	$width               = $width ? absint($width) : 0;
	$height              = $height ? absint($height) : 0;
	$upload_dir          = wp_upload_dir();
	$attachment_realpath = crb_normalize_path(get_attached_file($attachment_id));

	// Neu khong tim thay anh thi return lai placeholder de tranh bi loi
	if (empty($attachment_realpath)) {
		return "https://via.placeholder.com/{$width}x{$height}";
	}

	$filename  = basename($attachment_realpath);
	$fileParts = explode('.', $filename);

	// Kiem tra neu la nhung file anh dac biet nhu gif, svg thi khong xu ly
	$fileExt = $fileParts[count($fileParts) - 1];
	if (in_array($fileExt, ['gif', 'svg'])) {
		return wp_get_attachment_image_url($attachment_id, 'full');
	}

	// Kiem tra neu khach hang dang chon default hoac neu thiet bi su dung la iPhone hoac trinh duyet la Safari
	$agent = new Agent();
	if (get_option('_use_image_ext') === 'default' || $agent->is('iPhone')) {
		$extension = explode('.', $filename)[1];
	} else {
		$extension = get_option('_fixed_image_ext');
	}

	$filename = preg_replace('/(\.[^\.]+)$/', '-' . $width . 'x' . $height, $filename) . '.' . $extension;
	$filepath = crb_normalize_path($upload_dir['basedir'] . '/' . $filename);
	$url      = trailingslashit($upload_dir['baseurl']) . $filename;

	// Kiểm tra xem có ảnh đã resize hay chưa, nếu chưa có thì thực hiện resize
	if (!file_exists($filepath)) {
		resizeImage($attachment_realpath, $filepath, $width, $height, $extension);
		// Bổ sung vào metadata để sau này khi user xóa ảnh thì xóa luôn cả ảnh resize
		updateAttachmentSize($attachment_id, $filename, $width, $height, $extension);
	}

	return $url;
}

/**
 * Resize image by image's url without add_image_size
 *
 * @param      $url
 * @param null $width
 * @param null $height
 * @param bool $crop
 * @param bool $retina
 *
 * @return array|\WP_Error
 */
function resizeImageFly($url, $width = null, $height = null, $crop = true, $retina = false)
{
	global $wpdb;
	if (empty($url)) {
		return new WP_Error('no_image_url', __('No image URL has been entered.', 'wta'), $url);
	}
	// Get default size from database
	$width  = $width ? : get_option('thumbnail_size_w');
	$height = $height ? : get_option('thumbnail_size_h');
	// Allow for different retina sizes
	$retina = $retina ? ($retina === true ? 2 : $retina) : 1;
	// Get the image file path
	$file_path = parse_url($url);
	$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
	// Check for Multisite
	if (is_multisite()) {
		global $blog_id;
		$blog_details = get_blog_details($blog_id);
		$file_path    = str_replace($blog_details->path . 'files/', '/wp-content/blogs.dir/' . $blog_id . '/files/', $file_path);
	}
	// Destination width and height variables
	$dest_width  = $width * $retina;
	$dest_height = $height * $retina;
	// File name suffix (appended to original file name)
	$suffix = "{$dest_width}x{$dest_height}";
	// Some additional info about the image
	$info = pathinfo($file_path);
	$dir  = $info['dirname'];
	$ext  = $info['extension'];
	$name = wp_basename($file_path, ".$ext");
	if ('bmp' === $ext) {
		return new WP_Error('bmp_mime_type', __('Image is BMP. Please use either JPG or PNG.', 'wta'), $url);
	}
	// Suffix applied to filename
	$suffix = "{$dest_width}x{$dest_height}";
	// Get the destination file name
	$dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";
	if (!file_exists($dest_file_name)) {
		/*
		 *  Bail if this image isn't in the Media Library.
		 *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
		 */
		$query          = $wpdb->prepare("SELECT * FROM $wpdb->posts WHERE guid='%s'", $url);
		$get_attachment = $wpdb->get_results($query);
		if (!$get_attachment) {
			return ['url' => $url, 'width' => $width, 'height' => $height];
		}
		// Load Wordpress Image Editor
		$editor = wp_get_image_editor($file_path);
		if (is_wp_error($editor)) {
			return ['url' => $url, 'width' => $width, 'height' => $height];
		}
		// Get the original image size
		$size        = $editor->get_size();
		$orig_width  = $size['width'];
		$orig_height = $size['height'];
		$src_x       = $src_y = 0;
		$src_w       = $orig_width;
		$src_h       = $orig_height;
		if ($crop) {
			$cmp_x = $orig_width / $dest_width;
			$cmp_y = $orig_height / $dest_height;
			// Calculate x or y coordinate, and width or height of source
			if ($cmp_x > $cmp_y) {
				$src_w = round($orig_width / $cmp_x * $cmp_y);
				$src_x = round(($orig_width - ($orig_width / $cmp_x * $cmp_y)) / 2);
			} else {
				if ($cmp_y > $cmp_x) {
					$src_h = round($orig_height / $cmp_y * $cmp_x);
					$src_y = round(($orig_height - ($orig_height / $cmp_y * $cmp_x)) / 2);
				}
			}
		}
		// Time to crop the image!
		$editor->crop($src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height);
		// Now let's save the image
		$saved = $editor->save($dest_file_name);
		// Get resized image information
		$resized_url    = str_replace(basename($url), basename($saved['path']), $url);
		$resized_width  = $saved['width'];
		$resized_height = $saved['height'];
		$resized_type   = $saved['mime-type'];
		// Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
		$metadata = wp_get_attachment_metadata($get_attachment[0]->ID);
		if (isset($metadata['image_meta'])) {
			$metadata['image_meta']['resized_images'][] = $resized_width . 'x' . $resized_height;
			wp_update_attachment_metadata($get_attachment[0]->ID, $metadata);
		}
		// Create the image array
		$image_array = [
			'url'    => $resized_url,
			'width'  => $resized_width,
			'height' => $resized_height,
			'type'   => $resized_type,
		];
	} else {
		$image_array = [
			'url'    => str_replace(basename($url), basename($dest_file_name), $url),
			'width'  => $dest_width,
			'height' => $dest_height,
			'type'   => $ext,
		];
	}
	// Return image array
	return $image_array;
}

if (!function_exists('dd')) {
	function dd()
	{
		array_map(function ($x) {
			dump($x);
		}, func_get_args());
		die;
	}
}

/**
 * Google reCAPTCHA
 *
 **/
// add_action('login_enqueue_scripts', function () {
//     wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js');
// });
//
// if (!is_user_logged_in()) {
//     add_action('login_form', 'hda_recaptcha_field');
//     add_action('register_form', 'hda_recaptcha_field');
// }
// function hda_recaptcha_field()
// {
//     $sitekey = '6LcIaZUbAAAAANPrCHjuBXZ6FjWK4tJplXqyGnow';
//     echo '<div class="g-recaptcha" data-sitekey="' . $sitekey . '"></div>';
// }
// if (!is_user_logged_in()) {
//     add_filter('wp_authenticate_user', 'hkt_verify_recaptcha_on_login_register', 10, 3);
//     add_filter('registration_errors', 'hkt_verify_recaptcha_on_login_register', 10, 3);
// }
//
// function hda_verify_recaptcha_on_login_register($user = null, $password = null)
// {
//     $secretkey = "6LcIaZUbAAAAAE4TmOIp0kQs_tu3jbQrvTq-ghow";
//     if (isset($_POST['g-recaptcha-response'])) {
//         $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secretkey . '&response=' . $_POST['g-recaptcha-response'] . '&remoteip=' . $_SERVER['REMOTE_ADDR']);
//
//         $response = json_decode($response['body'], true);
//         if (true == $response['success']) {
//             return $user;
//         } else {
//             return new WP_Error('Captcha Invalid', __('ERROR: You are a bot'));
//         }
//     } else {
//         return new WP_Error('Captcha Invalid', __('ERROR: You are a bot. If not then enable JavaScript.'));
//     }
// }

update_option( 'siteurl', 'https://mooms.dev' );
update_option( 'home', 'https://mooms.dev' );

/**
 * Remove POST & COMMENt in menu Admin
 */
add_action('admin_init', 'hda_remove_admin_menus');
function hda_remove_admin_menus()
{
    remove_menu_page('edit-comments.php');
    remove_menu_page('edit.php');
}

/**
 * Convert Link YOUTUBE
 */
function getYoutubeEmbedUrl($url)
{
    $matches = [];
    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $matches);
    if (count($matches)) {
        $youtube_id = $matches[0];
    }
    return 'https://www.youtube.com/embed/' . $youtube_id;
}

/**
 *  Change plugin name
 */
add_action('admin_menu', 'myRenamedPlugin');
function myRenamedPlugin()
{
    global $menu;
    $searchPlugin = "contact-form-listing"; // Use the unique plugin name
    $replaceName = "Danh sách thông tin";
    $menuItem = "";
    foreach ($menu as $key => $item) {
        if ($item[2] === $searchPlugin) {
            $menuItem = $key;
        }
    }
    $menu[$menuItem][0] = $replaceName; // Position 0 stores the menu title
}

/**
 *  Hide Editor
 */
add_action('admin_init', 'hide_editor');
function hide_editor()
{
    // Get the Post ID.
    $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
    if (!isset($post_id)) return;
    // Hide the editor on the page titled 'Homepage'
    // $homepgname = get_the_title($post_id);
    // if($homepgname == 'Homepage'){
    //     remove_post_type_support('page', 'editor');
    // }
    // Hide the editor on a page with a specific page template
    // Get the name of the Page Template file.
    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    if ($template_file == 'page_templates/about_us_template.php') { // the filename of the page template
        remove_post_type_support('page', 'editor');
    }
}
