<?php

use MiniOrange\Helper\OauthDB as DB;
use MiniOrange\Helper\CustomerDetails as CD;
use MiniOrange\Helper\Lib\OauthAESEncryption;

if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['authorized']) && !empty($_SESSION['authorized'])) {
    if ($_SESSION['authorized'] != true) {
        header('Location: mo_oauth_admin_login.php');
        exit();
    }
}
else {
    header('Location: mo_oauth_admin_login.php');
    exit();
}

if (isset($_POST['option']) and $_POST['option'] == "mo_oauth_goto_login") {
    CD::delete_option('mo_oauth_new_registration');
    CD::update_option('mo_oauth_verify_customer', 'true');
}

if (isset($_POST['option']) and $_POST['option'] == "change_miniorange") {
   $customer = $customer = new Customeroauth();
    $customer->remove_account();
    mo_oauth_remove_account();
    DB::update_option('mo_oauth_message', 'Logged out of miniOrange account');
    mo_oauth_show_success_message();
    return;
}

if (isset($_POST['option']) and $_POST['option'] == "mo_oauth_go_back") {
    mo_oauth_remove_account();
}

if (isset($_POST['option']) and $_POST['option'] == "mo_oauth_verify_customer") { // register the admin to miniOrange

    if (!mo_oauth_is_curl_installed()) {
        DB::update_option('mo_oauth_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Login failed.');
        mo_oauth_show_error_message();

        return;
    }

    $email = '';
    $password = '';
    if (mo_oauth_check_empty_or_null($_POST['email']) || mo_oauth_check_empty_or_null($_POST['password'])) {
        DB::update_option('mo_oauth_message', 'All the fields are required. Please enter valid entries.');
        mo_oauth_show_error_message();

        return;
    } else if (moOauthcheckPasswordpattern(strip_tags($_POST['password']))) {
        DB::update_option('mo_oauth_message', 'Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*-_) should be present.');
        mo_oauth_show_error_message();
        return;
    } else {
        $email = $_POST['email'];
        $password = stripslashes(strip_tags($_POST['password']));
    }

    CD::update_option('mo_oauth_admin_email', $email);
    $customer = new Customeroauth();
    $content = $customer->get_customer_key();
    $customerKey = json_decode($content, true);
    if (json_last_error() == JSON_ERROR_NONE) {
        CD::update_option('mo_oauth_admin_customer_key', $customerKey['id']);
        CD::update_option('mo_oauth_admin_api_key', $customerKey['apiKey']);
        CD::update_option('mo_oauth_customer_token', $customerKey['token']);
        $certificate = DB::get_option('oauth_x509_certificate');
        DB::update_option('mo_oauth_message', 'Customer retrieved successfully');
        CD::update_option('mo_oauth_registration_status', 'logged');
        mo_oauth_show_success_message();
    } else {
        DB::update_option('mo_oauth_message', 'Invalid username or password. Please try again.');
        mo_oauth_show_error_message();
    }
}

if (isset($_POST['option']) && $_POST['option'] == 'mo_oauth_verify_license') {

    if (mo_oauth_check_empty_or_null($_POST['oauth_license_key'])) {
        DB::update_option('mo_oauth_message', 'All the fields are required. Please enter valid license key.');
        mo_oauth_show_error_message();
        return;
    }

    $code = trim($_POST['oauth_license_key']);
    $customer = new Customeroauth();
    $content = json_decode($customer->check_customer_ln(), true);
    if (strcasecmp($content['status'], 'SUCCESS') == 0) {
        $content = json_decode($customer->mo_oauth_vl($code, false), true);
        CD::update_option('vl_check_t', time());
        if (strcasecmp($content['status'], 'SUCCESS') == 0) {
            $key = CD::get_option('mo_oauth_customer_token');
            CD::update_option('sml_lk', OauthAESEncryption::encrypt_data($code, $key));
            DB::update_option('mo_oauth_message', 'Your license is verified. You can now configure the connector.');
            CD::update_option('mo_oauth_registration_status','verified');
            $key = CD::get_option('mo_oauth_customer_token');
            CD::update_option('site_ck_l', OauthAESEncryption::encrypt_data("true", $key));
            CD::update_option('t_site_status', OauthAESEncryption::encrypt_data("false", $key));
            //echo "string";exit;
            mo_oauth_show_success_message();
        } else if (strcasecmp($content['status'], 'FAILED') == 0) {
            if (strcasecmp($content['message'], 'Code has Expired') == 0) {
                DB::update_option('mo_oauth_message', 'License key you have entered has already been used. Please enter a key which has not been used before on any other instance or if you have exhausted all your keys, then buy more.');
            } else {
                DB::update_option('mo_oauth_message', 'You have entered an invalid license key. Please enter a valid license key.');
            }
            mo_oauth_show_error_message();
        } else {
            DB::update_option('mo_oauth_message', 'An error occured while processing your request. Please Try again.');
            mo_oauth_show_error_message();
        }
    } else {
        $key = CD::get_option('mo_oauth_customer_token');
        CD::update_option('site_ck_l', OauthAESEncryption::encrypt_data("false", $key));
        DB::update_option('mo_oauth_message', 'You have not upgraded yet. ');
        mo_oauth_show_error_message();
    }
}
?>
