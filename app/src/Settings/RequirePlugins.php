<?php

namespace App\Settings;

class RequirePlugins
{

	public function __construct()
	{
		require_once APP_DIR . 'activator_plugins/class-tgm-plugin-activation.php';
		add_action('tgmpa_register', [$this, 'registerRequirePlugins']);
	}

	public function registerRequirePlugins()
	{

		$plugins = [

			[
				'name'               => 'Classic Editor', // The plugin name.
				'slug'               => 'classic-editor', // The plugin slug (typically the folder name).
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			],
			[
				'name'               => 'Advanced Editor Tools', // The plugin name.
				'slug'               => 'tinymce-advanced', // The plugin slug (typically the folder name).
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			],
			[
				'name'               => 'Advanced CF7 DB', // The plugin name.
				'slug'               => 'advanced-cf7-db', // The plugin slug (typically the folder name).
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			],
			[
				'name'               => 'Contact form 7', // The plugin name.
				'slug'               => 'contact-form-7', // The plugin slug (typically the folder name).
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			],
			[
				'name'               => 'Rank Math SEO', // The plugin name.
				'slug'               => 'seo-by-rank-math', // The plugin slug (typically the folder name).
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			],
			[
				'name'               => 'Simple Custom Post Order', // The plugin name.
				'slug'               => 'simple-custom-post-order', // The plugin slug (typically the folder name).
				'required'           => true, // If false, the plugin is only 'recommended' instead of required.
				'force_activation'   => true, // If true, plugin is activated upon theme activation and cannot be deactivated until theme switch.
				'force_deactivation' => true, // If true, plugin is deactivated upon theme switch, useful for theme-specific plugins.
			],
		];

		$config = [
			'id'           => 'mooms',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'parent_slug'  => 'themes.php',            // Parent menu slug.
			'capability'   => 'edit_theme_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.


			'strings' => [
				'page_title'                      => __('Vui lòng cài đặt các gói mở rộng cần thiết', 'mooms'),
				'menu_title'                      => __('Gói mở rộng', 'mooms'),
				'installing'                      => __('Đang cài gói mở rộng: %s', 'mooms'),
				'updating'                        => __('Đang cập nhật gói mở rộng: %s', 'mooms'),
				'oops'                            => __('Có lỗi xảy ra trong quá trình giao tiếp với API của gói mở rộng.', 'mooms'),
				'notice_can_install_required'     => _n_noop('Bộ giao diện này yêu cầu cần phải cài đặt các gói mở rộng sau: %1$s.', 'Bộ giao diện này yêu cầu cần phải cài đặt các gói mở rộng sau: %1$s.', 'mooms'),
				'notice_can_install_recommended'  => _n_noop('Bộ giao diện này đề xuất cài đặt và sử dụng các gói mở rộng sau: %1$s.', 'Bộ giao diện này đề xuất cài đặt và sử dụng các gói mở rộng sau: %1$s.', 'mooms'),
				'notice_ask_to_update'            => _n_noop(
					'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
					'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
					'mooms'
				),
				'notice_ask_to_update_maybe'      => _n_noop(
					'There is an update available for: %1$s.',
					'There are updates available for the following plugins: %1$s.',
					'mooms'
				),
				'notice_can_activate_required'    => _n_noop(
					'The following required plugin is currently inactive: %1$s.',
					'The following required plugins are currently inactive: %1$s.',
					'mooms'
				),
				'notice_can_activate_recommended' => _n_noop(
					'The following recommended plugin is currently inactive: %1$s.',
					'The following recommended plugins are currently inactive: %1$s.',
					'mooms'
				),
				'install_link'                    => _n_noop(
					'Begin installing plugin',
					'Begin installing plugins',
					'mooms'
				),
				'update_link'                     => _n_noop(
					'Begin updating plugin',
					'Begin updating plugins',
					'mooms'
				),
				'activate_link'                   => _n_noop(
					'Begin activating plugin',
					'Begin activating plugins',
					'mooms'
				),
				'return'                          => __('Return to Required Plugins Installer', 'mooms'),
				'plugin_activated'                => __('Plugin activated successfully.', 'mooms'),
				'activated_successfully'          => __('The following plugin was activated successfully:', 'mooms'),
				'plugin_already_active'           => __('No action taken. Plugin %1$s was already active.', 'mooms'),
				'plugin_needs_higher_version'     => __('Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'mooms'),
				'complete'                        => __('All plugins installed and activated successfully. %1$s', 'mooms'),
				'dismiss'                         => __('Dismiss this notice', 'mooms'),
				'notice_cannot_install_activate'  => __('There are one or more required or recommended plugins to install, update or activate.', 'mooms'),
				'contact_admin'                   => __('Please contact the administrator of this site for help.', 'mooms'),
				'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
			],

		];

		tgmpa($plugins, $config);
	}
}

new RequirePlugins();
