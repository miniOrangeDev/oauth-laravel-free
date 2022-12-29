<?php

use MiniOrange\Helper\OauthDB as DB;
use MiniOrange\Helper\CustomerDetails as CD;


if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['authorized']) && !empty($_SESSION['authorized'])) {
    $user = DB::get_registered_user();
    if($user != NULL){
        if ($_SESSION['authorized'] == true) {
                header("Location: mo_oauth_setup.php");
                exit();
        }
    }
}
if (isset($_REQUEST['option']) && $_REQUEST['option'] == 'admin_login') {

    $email = '';
    $password = '';
    if (isset($_POST['email']) && !empty($_POST['email']))
        $email = $_POST['email'];
    if (isset($_POST['password']) && !empty($_POST['password']))
        $password = $_POST['password'];
    if (!empty($password)) {
        $password = sha1($password);
    }
    $user = DB::get_registered_user();
    $password_check = '';
    $email_check = '';
    if ($user != NULL)
        if (isset($user->password))
            $password_check = $user->password;
        else {
            $_SESSION['show_error_msg'] = true;
        }
    if ($user != NULL) {
        if (isset($user->email))
            $email_check = $user->email;
        else
            $_SESSION['show_error_msg'] = true;
    } 
    else if($user === NULL){
        $use_case = $_POST['use_case'];
        $customer = new Customeroauth();
        $content = $customer->get_customer_key();
        $customerKey = json_decode($content, true);
        if($customerKey != NULL){
            if(strcasecmp($customerKey['status'], 'SUCCESS') == 0){
                $customer->submit_register_user($email, $use_case);
                DB::register_user($email, $password);
                CD::update_option('mo_oauth_admin_email', $email);
                CD::update_option('mo_oauth_admin_customer_key', $customerKey['id']);
                CD::update_option('mo_oauth_use_case', $use_case);
                $_SESSION['authorized'] = true;
                if (isset($_SESSION['authorized']) && !empty($_SESSION['authorized'])) {
                    if ($_SESSION['authorized'] == true) {
                        header('Location: mo_oauth_setup.php');
                        exit;
                    }
                }
            }
        }
        else{
            if(strcasecmp($content, 'The customer is not valid ') === 0){
                CD::update_option('mo_oauth_message', 'Account does not exist. Please register');
            } else {
                CD::update_option('mo_oauth_message', $content);
            }
            $_SESSION['show_error_msg'] = true;
        }
    }

    if (!empty($password_check)) {
        if ($password === $password_check) {
        
            if (!isset($_SESSION['authorized']) || $_SESSION['authorized'] != true) {
                $_SESSION['authorized'] = true;
            }
            $_SESSION['admin_email'] = $email;
            header("Location: mo_oauth_setup.php");
            exit();
        } else {
            $_SESSION['invalid_credentials'] = true;
        }
    }
}

?>
