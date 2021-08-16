<?php

use App\Validators\Validator;
use Overtrue\Socialite\SocialiteManager;

define('SOCIAL_DRIVER', [
    'facebook' => [
        'client_id'     => '656235898501170',
        'client_secret' => '1a69cfc1df0c2b3da7cb7f0add20b6e2',
        'redirect'      => 'https://43factory.coffee/wp-admin/admin-ajax.php?action=social_login_callback&driver=facebook',
    ],
    'google'   => [
        'client_id'     => '497388776987-h6os1rkcoparsge40t06uitvkg620at0.apps.googleusercontent.com',
        'client_secret' => 'ikexbkpq8WEf90tGJiveI2So',
        'redirect'      => 'https://43factory.coffee/wp-admin/admin-ajax.php?action=social_login_callback&driver=google',
    ],
]);

add_action('wp_ajax_nopriv_user_login', 'stc_user_login');
add_action('wp_ajax_user_login', 'stc_user_login');
function stc_user_login() {
    if (empty($_POST)) {
        return '';
    }

    if (!wp_verify_nonce($_POST['_token'], 'user_dang_nhap')) {
        return __('Token mismatch!', 'mooms');
    }

    if (empty($_POST['user_login']) || empty($_POST['password'])) {
        return __('Your username or password does not match, please try again!', 'mooms');
    }

    $user = wp_signon([
        'user_login'    => $_POST['user_login'],
        'user_password' => $_POST['password'],
        'remember'      => true,
    ], false);

    if (is_wp_error($user)) {
        return $user->get_error_message();
    }

    echo '<script>window.location.href = "' . $_POST['redirect_to'] . '";</script>';
    return '';
    // wp_send_json_success(true);
}

add_action('wp_ajax_nopriv_job_seeker_register', 'stc_job_seeker_register');
add_action('wp_ajax_job_seeker_register', 'stc_job_seeker_register');
function stc_job_seeker_register() {
    /* Kiem tra captcha */
    //    $captcha = $_POST['g-recaptcha-response'];
    //    if (empty($captcha)) return [
    //      'status'   => false,
    //      'loi_nhan' => __("Bạn chưa nhập mã xác nhận (chọn vào I'm not robot)", 'mtdev'),
    //    ];
    //    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LfIuzYUAAAAADoy5KWNcnYkDumOexP1apz9Vv3v&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
    //    $response = json_decode($response, true);
    //    if (!$response['success']) return [
    //      'status'   => 'alert-success',
    //      'title'    => 'Cảnh báo',
    //      'loi_nhan' => __("Mã xác nhận chưa chính xác", 'mtdev'),
    //    ];

    /* Kiem tra dữ liệu truoc khi xu ly */
    if (!wp_verify_nonce($_POST['token'], 'stc_job_seeker_register')) {
        wp_send_json_error(__('Mã nhận dạng không chính xác', 'mooms'));
    }

    $validator = (new Validator())->make($_POST, [
        'email'                   => 'required|email',
        'username'                => 'required',
        'password'                => 'required',
        'password_confirm'        => 'required|same:password',
        'extras.first_name'       => 'required',
        'extras.last_name'        => 'required',
        'extras._phone_number'    => 'required',
        'extras._identify_number' => 'required',
    ]);

    if ($validator->fails()) {
        wp_send_json_error($validator->errors());
    }

    $idUser = stcCreateUser($_POST, 'job_seeker');

    if (is_wp_error($idUser)) {
        wp_send_json_error($idUser->get_error_message());
    }

    foreach ($_POST['extras'] as $metaKey => $metaValue) {
        updateUserMeta($idUser, $metaKey, $metaValue);
    }

    wp_send_json_success([
        'user_id'     => $idUser,
        'redirect_to' => '/tai-khoan/thong-tin',
    ]);
}

add_action('wp_ajax_nopriv_employer_register', 'stc_employer_register');
add_action('wp_ajax_employer_register', 'stc_employer_register');
function stc_employer_register() {
    /* Kiem tra dữ liệu truoc khi xu ly */
    if (!wp_verify_nonce($_POST['token'], 'stc_employer_register')) {
        wp_send_json_error(__('Mã nhận dạng không chính xác', 'mooms'));
    }

    $validator = (new Validator())->make($_POST, [
        'email'                => 'required|email',
        'username'             => 'required',
        'description'          => 'required',
        'password'             => 'required',
        'password_confirm'     => 'required|same:password',
        'extras.first_name'    => 'required',
        'extras.last_name'     => 'required',
        'extras._phone_number' => 'required',
        'extras._tax_code'     => 'required',
        'extras._company_name' => 'required',
        'extras._address'      => 'required',
        'extras._province'     => 'required',
        'extras._career'       => 'required',
        'extras._company_size' => 'required',
    ]);

    if ($validator->fails()) {
        wp_send_json_error($validator->errors());
    }

    $idUser = stcCreateUser($_POST, 'employer');

    if (is_wp_error($idUser)) {
        wp_send_json_error($idUser->get_error_message());
    }

    foreach ($_POST['extras'] as $metaKey => $metaValue) {
        updateUserMeta($idUser, $metaKey, $metaValue);
    }

    wp_send_json_success([
        'user_id'     => $idUser,
        'redirect_to' => '/tai-khoan/thong-tin',
    ]);
}

add_action('wp_ajax_nopriv_user_reset_password', 'stc_user_reset_password');
add_action('wp_ajax_user_reset_password', 'stc_user_reset_password');
function stc_user_reset_password() {
    wp_send_json_success(true);
}

add_action('wp_ajax_nopriv_google_login', 'googleLogin');
add_action('wp_ajax_google_login', 'googleLogin');
function googleLogin() {
    if (is_user_logged_in()) {
        socialCallbackRedirectUrl();
        die();
    }

    $socialite = new SocialiteManager(SOCIAL_DRIVER);
    $response  = $socialite->driver('google')->redirect();
    echo $response;
}

add_action('wp_ajax_nopriv_facebook_login', 'facebookLogin');
add_action('wp_ajax_facebook_login', 'facebookLogin');
function facebookLogin() {
    if (is_user_logged_in()) {
        socialCallbackRedirectUrl();
        die();
    }

    $socialite = new SocialiteManager(SOCIAL_DRIVER);
    $response  = $socialite->driver('facebook')->redirect();
    echo $response;
}

add_action('wp_ajax_nopriv_social_login_callback', 'facebookLoginCallback');
add_action('wp_ajax_social_login_callback', 'facebookLoginCallback');
function facebookLoginCallback() {
    if (is_user_logged_in()) {
        socialCallbackRedirectUrl();
        die();
    }

    try {
        $socialite = new SocialiteManager(SOCIAL_DRIVER);

        $fbUser = $socialite->driver($_GET['driver'])->user();

        $args = [
            'id'       => $fbUser->getId(),           // 1472352
            'nickname' => $fbUser->getNickname(),     // "overtrue"
            'username' => $fbUser->getName(),         // "overtrue"
            'name'     => $fbUser->getName(),         // "安正超"
            'email'    => $fbUser->getEmail(),        // "anzhengchao@gmail.com"
            'provider' => $fbUser->getProviderName(), // GitHub
        ];

        $user = get_user_by_email($args['email']);
        if (!$user) {
            $userId = wp_insert_user([
                'user_login'   => $args['email'],
                'user_email'   => $args['email'],
                'display_name' => $args['username'],
            ]);

            if (!is_wp_error($userId)) {
                updateUserMeta($userId, 'billing_first_name', $args['name']);
                updateUserMeta($userId, 'billing_email', $args['email']);

                $user = get_user_by_email($args['email']);
            }
        }

        wp_set_current_user($user->ID, $user->user_login);
        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $user->user_login, $user);
        socialCallbackRedirectUrl();
    } catch (\Exception $ex) {
        dump($ex);
    }
}

function socialCallbackRedirectUrl() {
    echo '<script>console.log(opener); opener.socialLoginReturn({
                success: true,
                redirect: "/"
            });window.close();</script>';
}

function stcCreateUser($data, $role) {
    $userParams = [
        'user_login' => $data['username'],
        'user_email' => $data['email'],
        'user_pass'  => $data['password'],
        'role'       => $role,
    ];

    switch ($role) {
        case 'employer':
            $userParams['display_name'] = $data['_company_name'];
            break;
        case 'job_seeker':
            $userParams['display_name'] = $data['last_name'] . ' ' . $data['first_name'];
            break;
        default:
            break;
    }

    return wp_insert_user($userParams);
}

function stcIsJobSeekerAccount() {
    $user = wp_get_current_user();

    if ($user === null) {
        return false;
    }

    foreach ($user->roles as $role) {
        if (in_array($role, ['job_seeker', 'administrator'])) {
            return true;
        }
    }

    return false;
}

function stcIsEmployerAccount() {
    $user = wp_get_current_user();

    if ($user === null) {
        return false;
    }

    foreach ($user->roles as $role) {
        if (in_array($role, ['employer', 'administrator'])) {
            return true;
        }
    }

    return false;
}
