<?php

namespace App\Settings;

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Intervention\Image\ImageManagerStatic as Image;

class AdminSettings
{
    protected $currentUser;

    protected $superUsers = SUPER_USER;

    protected $errorMessage = '';

    public function __construct()
    {
        $this->currentUser = wp_get_current_user();

        if (in_array($this->currentUser->user_login, $this->superUsers, true)) {
            $this->createAdminOptions();
        } else {
            $this->hideSuperUsers();
            $this->setupErrorMessage();
            $this->checkIsMaintenance();
            $this->disablePluginPage();
            $this->disableOptionsReadPage();
            $this->disableAllUpdate();
            $this->removeUnnecessaryMenus();
        }

        $this->addDashboardContactWidget();
        $this->changeFontRoboto();
        $this->replaceWordpressLogo();
        $this->removeDefaultWidgets();
        $this->removeDashboardWidgets();
        $this->changeHeaderUrl();
        $this->changeHeaderTitle();
        $this->changeFooterCopyright();
        $this->customizeAdminBar();
        $this->resizeOriginalImageAfterUpload();
        $this->renameUploadFileName();
        $this->addCustomResources();
        $this->addCustomExtensionsInMediaUpload();

        if (get_option('_disable_admin_confirm_email') === 'yes') {
            $this->disableChangeAdminEmailRequireConfirm();
        }

        if (get_option('_disable_use_weak_password') === 'yes') {
            $this->disableCheckboxUseWeakPassword();
        }
    }

    public function addCustomExtensionsInMediaUpload()
    {
        add_filter('upload_mimes', static function ($mimes) {
            return array_merge($mimes, [
                'ac3' => 'audio/ac3',
                'mpa' => 'audio/MPA',
                'flv' => 'video/x-flv',
                'svg' => 'image/svg+xml',
            ]);
        });

        add_action('wp_ajax_stc_get_attachment_url_thumbnail', static function () {
            $url          = '';
            $attachmentID = isset($_REQUEST['attachmentID']) ? $_REQUEST['attachmentID'] : '';
            if ($attachmentID) {
                $url = wp_get_attachment_url($attachmentID);
            }
            die($url);
        });
    }

    public function disableCheckboxUseWeakPassword()
    {
        add_action('admin_head', function () {
?>
            <script>
                jQuery(document).ready(function() {
                    jQuery('.pw-weak').remove();
                });
            </script>
        <?php
        });

        add_action('login_enqueue_scripts', function () {
        ?>
            <script>
                document.addEventListener("DOMContentLoaded", function(event) {
                    let elements = document.getElementsByClassName('pw-weak');
                    console.log(elements);
                    let requiredElement = elements[0];
                    requiredElement.remove();
                });
            </script>
            <?php
        });
    }

    public function addDashboardContactWidget()
    {
        add_action('wp_dashboard_setup', static function () {
            wp_add_dashboard_widget('custom_help_widget', 'Gi???i thi???u', static function () { ?>
                <div style="position: relative;">
                    <div style="text-align:center">
                        <a target="_blank" href="<?php echo AUTHOR['website'] ?>" title="<?php echo AUTHOR['name'] ?>">
                            <img style="width:100%" src="<?php echo AUTHOR['logo_color_url'] ?>" alt="<?php echo AUTHOR['name'] ?>" title="<?php echo AUTHOR['name'] ?>">
                        </a>
                    </div>
                    <h2 style="text-align:center;"><?php echo AUTHOR['name'] ?></h2>
                    <div style="margin-top:20px">
                        <h3 style="text-align:center;"><strong>TH??NG TIN NH?? PH??T TRI???N</strong></h3>
                        <!--<p>H??? th???ng ???????c ph??t tri???n b???i <a target="_blank" href="--><?php //echo AUTHOR['website'] ?><!--"><strong>--><?php //echo AUTHOR['name'] ?><!--</strong></a></p>-->
                        <!--<p>M???i y??u c???u, h??? tr??? qu?? kh??ch vui l??ng li??n h???:</p>-->
                        <p><strong>??i???n tho???i</strong>: <a href="tel:<?php echo AUTHOR['phone_number'] ?>" style="color:red"><?php echo AUTHOR['phone_number'] ?></a></p>
                        <p><strong>Email</strong>: <a style="color:red" href="mailto:<?php echo AUTHOR['email'] ?>"><?php echo AUTHOR['email'] ?></a></p>
                        <p><strong>Website</strong>: <a style="color:red" target="_blank" href="<?php echo AUTHOR['website'] ?>"><?php echo AUTHOR['website'] ?></a></p>
                    </div>
                    <p><strong>C???m ??n qu?? kh??ch ???? tin t?????ng v?? s??? d???ng s???n ph???m c???a ch??ng t??i.</strong></p>
                </div>
            <?php });
        });
    }

    public function changeFontRoboto()
    {
        add_action('admin_head', function () { ?>
            <script src="//ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
            <script>
                WebFont.load({
                    google: {
                        'families': [
                            "Montserrat:300,400,500,600,700,800,900",
                            'Nunito:200,300,400,600,700,800,900',
                            'Roboto:300,400,500,600,700'
                        ]
                    },
                    active: function() {
                        sessionStorage.fonts = true;
                    }
                });
            </script>

            <style>
                html,
                body {
                    /*font-family : Roboto, sans-serif !important;*/
                    font-family: 'Quicksand', sans-serif;
                    font-weight: 400;
                    font-size: 15px;
                }
            </style>

        <?php });
    }

    public function replaceWordpressLogo()
    {
        add_action('wp_before_admin_bar_render', static function () {
        ?>
            <style type="text/css">

            </style>
<?php
        }, 0);
    }

    public function removeDefaultWidgets()
    {
        add_action('widgets_init', static function () {
            unregister_widget('WP_Widget_Pages');
            unregister_widget('WP_Widget_Calendar');
            unregister_widget('WP_Widget_Archives');
            unregister_widget('WP_Widget_Links');
            unregister_widget('WP_Widget_Meta');
            unregister_widget('WP_Widget_Search');
            unregister_widget('WP_Widget_Categories');
            unregister_widget('WP_Widget_Recent_Posts');
            unregister_widget('WP_Widget_Recent_Comments');
            unregister_widget('WP_Widget_RSS');
            unregister_widget('WP_Widget_Tag_Cloud');
            unregister_widget('WP_Nav_Menu_Widget');
        });
    }

    public function removeDashboardWidgets()
    {
        add_action('admin_init', static function () {
            remove_meta_box('dashboard_right_now', 'dashboard', 'normal');       // right now
            remove_meta_box('dashboard_activity', 'dashboard', 'normal');        // WP 3.8
            remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // recent comments
            remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // incoming links
            remove_meta_box('dashboard_plugins', 'dashboard', 'normal');         // plugins
            remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');     // quick press
            remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');   // recent drafts
            remove_meta_box('dashboard_primary', 'dashboard', 'normal');         // wordpress blog
            remove_meta_box('dashboard_secondary', 'dashboard', 'normal');       // other wordpress news
        });
    }

    public function changeHeaderUrl()
    {
        add_filter('login_headerurl', static function ($url) {
            return '' . AUTHOR['website'] . '';
        });
    }

    public function changeHeaderTitle()
    {
        add_filter('login_headertitle', static function () {
            return get_option('blogname');
        });
    }

    public function changeFooterCopyright()
    {
        add_filter('admin_footer_text', static function () {
            echo 'Website ???????c ph??t tri???n b???i <a href="' . AUTHOR['website'] . '" target="_blank">' . AUTHOR['name'] . '</a>';
        });
    }

    public function customizeAdminBar()
    {
        $author = AUTHOR;
        add_action('wp_before_admin_bar_render', static function () use ($author) {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('wp-logo');          // Remove the Wordpress logo
            $wp_admin_bar->remove_menu('about');            // Remove the about Wordpress link
            $wp_admin_bar->remove_menu('wporg');            // Remove the Wordpress.org link
            $wp_admin_bar->remove_menu('documentation');    // Remove the Wordpress documentation link
            $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
            $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
            // $wp_admin_bar->remove_menu('site-name');        // Remove the site name menu
            $wp_admin_bar->remove_menu('view-site');        // Remove the view site link
            $wp_admin_bar->remove_menu('updates');          // Remove the updates link
            $wp_admin_bar->remove_menu('comments');         // Remove the comments link
            $wp_admin_bar->remove_menu('new-content');      // Remove the content link
            $wp_admin_bar->remove_menu('w3tc');             // If you use w3 total cache remove the performance link
            // $wp_admin_bar->remove_menu('my-account');       // Remove the user details tab
        }, 7);

        add_action('admin_bar_menu', static function ($wp_admin_bar) use ($author) {
            $args = [
                'id'    => 'logo_author',
                'title' => '<img src="' . $author['logo_url'] . '" style="height:25px;padding-top:3px;">',
                'href'  => $author['website'],
                'meta'  => [
                    'target' => '_blank',
                ],
            ];
            $wp_admin_bar->add_node($args);

            // $args = [
            //     'id'    => 'theme_author',
            //     'title' => $author['name'],
            //     'href'  => $author['website'],
            //     'meta'  => [
            //         'target' => '_blank',
            //     ],
            // ];
            // $wp_admin_bar->add_node($args);
        }, 10);
    }

    public function renameUploadFileName()
    {
        add_filter('sanitize_file_name', function ($filename) {
            $info        = pathinfo($filename);
            $ext         = empty($info['extension']) ? '' : '.' . $info['extension'];
            $newFileName = str_replace($ext, '', date('YmdHi') . '-' . $filename);
            $unicode     = [
                'a' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
                'd' => '??',
                'e' => '??|??|???|???|???|??|???|???|???|???|???',
                'i' => '??|??|???|??|???',
                'o' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
                'u' => '??|??|???|??|???|??|???|???|???|???|???',
                'y' => '??|???|???|???|???',
                'A' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
                'D' => '??',
                'E' => '??|??|???|???|???|??|???|???|???|???|???',
                'I' => '??|??|???|??|???',
                'O' => '??|??|???|??|???|??|???|???|???|???|???|??|???|???|???|???|???',
                'U' => '??|??|???|??|???|??|???|???|???|???|???',
                'Y' => '??|???|???|???|???',
            ];
            foreach ($unicode as $nonUnicode => $uni) {
                $newFileName = preg_replace("/($uni)/i", $nonUnicode, $newFileName);
            }
            $newFileName = str_replace(' ', '-', $newFileName);
            $newFileName = preg_replace('/[^A-Za-z0-9\-]/', '', $newFileName);
            $newFileName = preg_replace('/-+/', '-', $newFileName);
            return $newFileName . $ext;
        }, 10);
    }

    public function resizeOriginalImageAfterUpload()
    {
        add_filter('intermediate_image_sizes_advanced', static function ($sizes) {
            $imgSize = [
                // 'medium',
                // 'medium_large',
                // 'large',
                // 'full',
                // 'woocommerce_single',
                // 'woocommerce_gallery_thumbnail',
                // 'shop_catalog',
                // 'shop_single',
                // 'woocommerce_thumbnail',
                // 'shop_thumbnail',
            ];
            foreach ($imgSize as $item) {
                if (array_key_exists($item, $sizes)) {
                    unset($sizes[$item]);
                }
            }
            return $sizes;
        });

        add_filter('wp_generate_attachment_metadata', static function ($image_data) {
            try {
                $upload_dir = wp_upload_dir();
                $imgPath    = $upload_dir['basedir'] . '/' . $image_data['file'];
                $image      = Image::make($imgPath);
                $imgWidth   = $image->width();
                $imgHeight  = $image->height();
                if (($imgWidth > $imgHeight) && ($imgWidth > 1920)) {
                    $image->resize(1920, null, static function ($constraint) {
                        $constraint->aspectRatio();
                    });
                } elseif (($imgHeight > $imgWidth) && ($imgHeight > 1080)) {
                    $image->resize(null, 1080, static function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $image->save($imgPath, 85);
            } catch (\Exception $ex) {
            }
            return $image_data;
        });
    }

    public function addCustomResources()
    {
        add_action('admin_enqueue_scripts', static function ($hook) {
            // wp_enqueue_style('mooms-custom-style', adminAsset('css/admin.css'));
            wp_enqueue_script('jquery_repeater', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js');
            wp_enqueue_script('mooms-custom-scripts', adminAsset('js/admin.js'));
        });
    }

    public function disableChangeAdminEmailRequireConfirm()
    {
        remove_action('add_option_new_admin_email', 'update_option_new_admin_email');
        remove_action('update_option_new_admin_email', 'update_option_new_admin_email');

        add_action('add_option_new_admin_email', function ($old_value, $value) {
            update_option('admin_email', $value);
        }, 10, 2);

        add_action('update_option_new_admin_email', function ($old_value, $value) {
            update_option('admin_email', $value);
        }, 10, 2);
    }

    public function createAdminOptions()
    {
        add_action('carbon_fields_register_fields', static function () {
            $options = Container::make('theme_options', __('Admin', 'mooms'))
                ->set_page_file(__('mooms-admin-settings', 'mooms'))
                ->set_page_menu_position(2)
                ->add_tab(__('ADMIN', 'mooms'), [
                    Field::make('checkbox', 'is_maintenance', __('B???t ch??? ????? b???o tr?? website', 'mooms')),
                    Field::make('checkbox', 'use_rank_math_breadcrumb', __('S??? d???ng Rank Math breadcrumb', 'mooms')),
                    Field::make('checkbox', 'disable_admin_confirm_email', __('T???t t??nh n??ng ?????i email admin c???n ph???i x??c minh email', 'mooms')) ->set_default_value('true'),
                    Field::make('checkbox', 'disable_use_weak_password', __('T???t t??nh n??ng cho ph??p s??? d???ng m???t kh???u y???u', 'mooms')),
                    Field::make('checkbox', 'use_short_url', __('S??? d???ng ???????ng d???n t???t (Lo???i b??? th?? m???c wp-content/theme. Ch?? ?? ??i???u ch???nh file .htaccess nh?? h?????ng d???n)', 'mooms')),
                    Field::make('separator', 'gm_sep_1', __('T??y ch???nh render ???nh', 'mooms')),
                    Field::make('checkbox', 'use_php_image_magick', __('S??? d???ng th?? vi???n PHP ImageMagick ????? x??? l?? ???nh', 'mooms')),
                    Field::make('radio', 'use_image_ext', __('Render chu???n ???nh')) ->set_width(50) ->set_default_value('default')
                    ->set_options([
                        'default' => __('D??ng chu???n m???c ?????nh c???a ???nh', 'mooms'),
                        'fixed'   => __('Render ra chu???n ???nh c??? ?????nh', 'mooms'),
                    ]),
                    Field::make('text', 'fixed_image_ext', __('Chu???n ???nh c??? ?????nh', 'mooms'))->set_default_value('webp'),
                ])
                ->add_tab(__('SMTP', 'mooms'), [
                    Field::make('checkbox', 'use_smtp', __('S??? d???ng SMTP ????? g???i mail', 'mooms')),
                    Field::make('separator', 'smtp_separator_1', __('Th??ng tin m??y ch??? SMTP', 'mooms')),
                    Field::make('text', 'smtp_host', __('?????a ch??? m??y ch???', 'mooms')) ->set_default_value('smtp.gmail.com'),
                    Field::make('text', 'smtp_port', __('C???ng m??y ch???', 'mooms'))  ->set_default_value('587'),
                    Field::make('text', 'smtp_secure', __('Ph????ng th???c m?? h??a', 'mooms'))   ->set_default_value('TLS'),
                    Field::make('separator', 'smtp_separator_2', __('Th??ng tin email h??? th???ng', 'mooms')),
                    Field::make('text', 'smtp_username', __('?????a ch??? email', 'mooms')) ->set_default_value('mooms.dev@gmail.com'),
                    Field::make('text', 'smtp_password', __('M???t kh???u', 'mooms')) ->set_default_value('utakxthdfibquxos'),
                ])
                ->add_tab(__('Theme info', 'mooms'), [
                    Field::make('text', 'theme_info_name', __('T??n', 'mooms')) ->set_attribute('readOnly','Blog Du l???ch - Website MOOMS.DEV'),
                    Field::make('text', 'theme_info_email', __('Email', 'mooms')) ->set_width(50) ->set_attribute('readOnly','mooms.dev@gmail.com'),
                    Field::make('text', 'theme_info_phone_number', __('??i???n tho???i', 'mooms')) ->set_width(50) ->set_attribute('readOnly','0989 64 67 66'),
                    Field::make('text', 'theme_info_logo_url', __('Link logo', 'mooms')) ->set_width(50) ->set_attribute('readOnly','mooms.dev/logo.png'),
                    Field::make('text', 'theme_info_website', __('Website h??? tr???', 'mooms')) ->set_width(50) ->set_attribute('readOnly','https://mooms.dev'),
                ]);

        });
    }

    public function hideSuperUsers()
    {
        add_action('pre_user_query', function ($user_search) {
            global $wpdb;
            $superUsers               = "('" . implode("','", $this->superUsers) . "')";
            $user_search->query_where = str_replace('WHERE 1=1', "WHERE 1=1 AND {$wpdb->users}.user_login NOT IN " . $superUsers, $user_search->query_where);
        });
    }

    public function setupErrorMessage()
    {
        $this->errorMessage = '
            	    <div style="position: relative;">
						<div style="text-align:center">
							<a target="_blank" href="' . AUTHOR['website'] . '">
								<img style="width:50%" src="' . AUTHOR['logo_color_url'] . '" alt="' . AUTHOR['name'] . '">
							</a>
						</div>
						<h1 style="text-align: center; text-transform: uppercase">Xin l???i, b???n kh??ng c?? quy???n truy c???p v??o n???i dung n??y</h1>
						<h2  style="text-align: center;"><a href="/wp-admin/">Quay v??? trang qu???n tr???</a></h2>
					</div>';
    }

    public function checkIsMaintenance()
    {
        add_action('after_setup_theme', static function () {
            if (get_option('_is_maintenance') === 'yes') {
                wp_die('
					<div style="position: relative;">
						<div style="text-align:center">
							<a target="_blank" href="' . AUTHOR['website'] . '">
								<img style="width:50%" src="' . AUTHOR['logo_url'] . '" alt="' . AUTHOR['name'] . '">
							</a>
						</div>
						<h2 style="text-align:center">H??? th???ng hi???n ??ang ???????c b???o tr??, xin qu?? kh??ch vui l??ng quay l???i sau ??t ph??t.</h2>
						<div style="text-align:center">
							<h3>H??? th???ng ???????c ph??t tri???n b???i <a target="_blank" href="' . AUTHOR['website'] . '"><strong>' . AUTHOR['name'] . '</strong></a></h3>
							<p>M???i y??u c???u, h??? tr??? qu?? kh??ch h??ng c?? th??? li??n h???</p>
							<p><strong>??i???n tho???i</strong>: <a href="tel:' . AUTHOR['phone_number'] . '" style="color:red">' . AUTHOR['phone_number'] . '</a></p>
							<p><strong>Email</strong>: <a style="color:red" href="mailto:' . AUTHOR['email'] . '">' . AUTHOR['email'] . '</a></p>
						</div>
					</div>');
            }
        });
    }

    public function disablePluginPage()
    {
        add_action('admin_menu', static function () {
            global $menu;
            foreach ($menu as $key => $menuItem) {
                switch ($menuItem[2]) {
                    case 'plugins.php':
                    case 'customize.php':
                        // case 'themes.php':
                        unset($menu[$key]);
                        break;
                }
            }

            global $submenu;
            unset($submenu['themes.php'][5], $submenu['themes.php'][6], $submenu['themes.php'][11]);
        }, 999);

        $errorMessage = $this->errorMessage;
        add_action('current_screen', static function () use ($errorMessage) {
            $deniePage      = [
                'plugins',
                'plugin-install',
                'plugin-editor',
                'themes',
                'theme-install',
                'theme-editor',
                'customize',
                'tools',
                'import',
                'export',
                'tools_page_action-scheduler',
                'tools_page_export_personal_data',
                'tools_page_remove_personal_data',
            ];
            $current_screen = get_current_screen();
            // dump($current_screen);
            if ($current_screen !== null && in_array($current_screen->id, $deniePage, true)) {
                wp_die($errorMessage);
            }
        });
    }

    public function disableOptionsReadPage()
    {
        //	    $denyPages = [];
        $removePages = [
            'options-reading.php',
            'options-writing.php',
            'options-discussion.php',
            'options-media.php',
            'privacy.php',
            'options-permalink.php',
            'tinymce-advanced',
        ];
        add_action('admin_menu', static function () use ($removePages) {
            foreach ($removePages as $page) {
                remove_submenu_page('options-general.php', $page);
            }
            //            global $submenu;
            //            dump($submenu);
            //            remove_submenu_page('options-general.php', 'options-writing.php');
            //            remove_submenu_page('options-general.php', 'options-discussion.php');
            //            remove_submenu_page('options-general.php', 'options-media.php');
            //            remove_submenu_page('options-general.php', 'privacy.php');
        });

        $errorMessage = $this->errorMessage;
        $denyPages    = [
            'options-reading',
            'options-writing',
            'options-discussion',
            'options-media',
            'privacy',
            'options-permalink',
            'settings_page_tinymce-advanced',
            'toplevel_page_wpseo_dashboard',
        ];
        add_action('current_screen', static function () use ($errorMessage, $denyPages) {
            $current_screen = get_current_screen();
            if ($current_screen !== null && in_array($current_screen->id, $denyPages, true)) {
                wp_die($errorMessage);
            }
        });
    }

    public function disableAllUpdate()
    {
        remove_action('load-update-core.php', 'wp_update_plugins');
        add_filter('pre_site_transient_update_plugins', create_function('$a', "return null;"));
    }

    public function removeUnnecessaryMenus()
    {
        add_action('admin_menu', static function () {
            global $menu;
            global $submenu;
            foreach ($menu as $key => $menuItem) {
                if (in_array($menuItem[2], [
                    'tools.php',
                    'edit-comments.php',
                    'wpseo_dashboard',
                    'duplicator',
                    'yit_plugin_panel',
                    'woocommerce-checkout-manager',
                    //'options-general.php',
                ])) {
                    unset($menu[$key]);
                }
            }
        });
    }
}
