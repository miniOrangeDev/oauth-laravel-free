<?php
if (session_status() == PHP_SESSION_NONE) {
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

if (isset($_REQUEST['option'])) {
    header('Location: mo_oauth_admin_login.php');
    exit();
}

?>
