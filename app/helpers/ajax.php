<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Hàm ajax để bật / tắt bài viết nổi bật
 */

use Illuminate\Support\Facades\Request;

// add_action('wp_ajax_toggle_is_feature', 'toggleIsFeature');
// function toggleIsFeature()
// {
// 	if (empty($_POST['post_id'])) {
// 		wp_send_json_error('Post id mismatch');
// 	}
// 	$postId    = $_POST['post_id'];
// 	$isFeature = carbon_get_post_meta($postId, 'is_feature');
// 	carbon_set_post_meta($postId, 'is_feature', !$isFeature);
// 	wp_send_json_success(true);
// }

/**
 * Ham ajax xu ly custom sort order
 */
add_action('wp_ajax_update_custom_sort_order', 'updateCustomSortOrder');
function updateCustomSortOrder()
{
	if (empty($_POST['post_ids']) || empty($_POST['current_page'])) {
		wp_send_json_error();
	}

	$postIds     = $_POST['post_ids'];
	$currentPage = (int)$_POST['current_page'];
	$order       = (($currentPage - 1) * count($postIds)) + 1;
	foreach ($postIds as $postId) {
		wp_update_post([
			'ID'         => $postId,
			'menu_order' => $order,
		]);
		$order++;
	}

	wp_send_json_success();
}

/**
 * Ham ajax update post thumbnail id
 */
add_action('wp_ajax_nopriv_update_post_thumbnail_id', 'updatePostThumbnailId');
add_action('wp_ajax_update_post_thumbnail_id', 'updatePostThumbnailId');
function updatePostThumbnailId()
{
	if (empty($_POST['post_id']) || empty($_POST['attachment_id'])) {
		wp_send_json_error();
	}

	$postId       = $_POST['post_id'];
	$attachmentId = $_POST['attachment_id'];

	updatePostMeta($postId, '_thumbnail_id', $attachmentId);

	wp_send_json_success(true);
}

/**
 * Hàm ajax send form liên hệ.
 * Các thông số truyền lên bao gồm: action, _token, name, email, phone_number, subject, message
 */
add_action('wp_ajax_nopriv_send_contact_form', 'sendContactForm');
add_action('wp_ajax_send_contact_form', 'sendContactForm');
function sendContactForm()
{
	if (empty($_POST['_token']) || !wp_verify_nonce($_POST['_token'], 'send_contact_form')) {
		wp_send_json_error(__('Token mistake.'));
	}

	$blogName = get_bloginfo('name');
	$blogUrl  = get_bloginfo('url');

	$html = "<p>Send from: {$_POST['name']} {$_POST['email']}</p>
             <p>Contact phone number: {$_POST['phone_number']}</p>
             <p>Subject: {$_POST['subject']}</p>
             <p>Contact message:</p>
             <p>{$_POST['message']}</p>
             <p>This email is sent from the contact form of the {$blogName} ({$blogUrl})</p>";

	$headers = [
		'Content-Type: text/html; charset=UTF-8',
		"Reply-To: {$_POST['name']} <{$_POST['email']}>",
	];

	$success = wp_mail(get_option('admin_email'), $blogName . ': ' . $_POST['subject'], $html, $headers);

	if ($success) {
		wp_send_json_success([
			'message' => __('Yêu cầu của bạn đã được hệ thống ghi nhận.', 'mooms'),
		]);
	}

	wp_send_json_error([
		'message' => __('Đã có lỗi xảy ra, xin vui lòng thử lại.', 'mooms'),
	]);
}

add_action('wp_ajax_nopriv_faker_posts', 'fakerPosts');
add_action('wp_ajax_faker_posts', 'fakerPosts');
function fakerPosts()
{
	// $faker = new \mooms\Settings\FakerData();
	$faker = new \App\Settings\FakerData();
	$faker->createSamplePosts('post', 2);
	wp_send_json_success(true);
}

add_action('wp_ajax_nopriv_get_page', 'ajaxGetPage');
add_action('wp_ajax_get_page', 'ajaxGetPage');
function ajaxGetPage()
{
	ob_start();
	get_template_part('page');
	$content = ob_get_contents();
	ob_clean();

	wp_send_json_success($content);
}
