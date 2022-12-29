<?php

use Illuminate\Support\Facades\Response;
use MiniOrange\Helper\OauthDB as DB;

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_POST['option']) && !empty($_POST['option'])) {

    $email = '';
    $password = '';
    if (isset($_POST['email']) && !empty($_POST['email']))
        $email = $_POST['email'];
    if (isset($_POST['password']) && !empty($_POST['password']))
        $password = $_POST['password'];
    if (!empty($password)) {
        $password = sha1($password);
    }
    if ($_POST['option'] === 'register') {
        $response = mo_oauth_register_action();
        if( isset($response['status']) && $response['status'] === 'error' ) {
            $_SESSION['show_error_msg'] = true;
        } else{
            DB::register_user($email, $password);
        }
    }
}
if (isset($_SESSION)) {
    if (mo_oauth_is_user_registered()) {
        $_SESSION['authorized'] = true;
        if (isset($_SESSION['authorized']) && !empty($_SESSION['authorized'])) {
            if ($_SESSION['authorized'] == true) {
                header('Location: mo_oauth_setup.php');
                exit;
            }
        }
    }
}
?>
