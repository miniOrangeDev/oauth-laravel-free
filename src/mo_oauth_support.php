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

if (isset($_POST['option']) and $_POST['option'] == "mo_oauth_contact_us_query_option") {

    if (!mo_oauth_is_curl_installed()) {
        DB::update_option('mo_oauth_message', 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> is not installed or disabled. Query submit failed.');
        mo_oauth_show_error_message();

        return;
    }

    // Contact Us query
    $email = $_POST['mo_oauth_contact_us_email'];
    $phone = $_POST['mo_oauth_contact_us_phone'];
    $query = $_POST['mo_oauth_contact_us_query'];
    $customer = new Customeroauth();
    if (mo_oauth_check_empty_or_null($email) || mo_oauth_check_empty_or_null($query)) {
        DB::update_option('mo_oauth_message', 'Please fill up Email and Query fields to submit your query.');
        mo_oauth_show_error_message();
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        DB::update_option('mo_oauth_message', 'Please enter a valid email address.');
        mo_oauth_show_error_message();
    } else {
        $submited = $customer->submit_contact_us($email, $phone, $query);
        if ($submited == false) {
            DB::update_option('mo_oauth_message', 'Your query could not be submitted. Please try again.');
            mo_oauth_show_error_message();
        } else {
            DB::update_option('mo_oauth_message', 'Thanks for getting in touch! We shall get back to you shortly.');
            mo_oauth_show_success_message();
        }
    }
}
?>