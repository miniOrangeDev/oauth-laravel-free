<?php

use MiniOrange\Helper\OauthDB as DB;

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

if (isset($_POST['option']) and $_POST['option'] == "mo_oauth_trial_request_option") {

    if (!mo_oauth_is_curl_installed()) {
        DB::update_option('mo_oauth_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. use case submit failed.');
        mo_oauth_show_error_message();

        return;
    }
    //Trial/Demo Request
    $email = $_POST['mo_oauth_trial_request_email'];
    $use_case = $_POST['mo_oauth_trial_request_use_case'];
    $customer = new Customeroauth();
    if (mo_oauth_check_empty_or_null($email) || mo_oauth_check_empty_or_null($use_case)) {
        DB::update_option('mo_oauth_message', 'Please fill up Email and Use Case fields to submit your use case.');
        mo_oauth_show_error_message();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        DB::update_option('mo_oauth_message', 'Please enter a valid email address.');
        mo_oauth_show_error_message();
    } else {
        $submited = $customer->submit_trial_request($email, $use_case);
        if ($submited == false) {
            DB::update_option('mo_oauth_message', 'Your use case could not be submitted. Please try again.');
            mo_oauth_show_error_message();
        } else {
            DB::update_option('mo_oauth_message', 'Thanks for getting in touch! We shall get back to you shortly.');
            mo_oauth_show_success_message();
        }
    }
}
?>