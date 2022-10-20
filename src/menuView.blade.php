<?php use MiniOrange\Helper\DB as DB;?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css"
          href="miniorange/sso/includes/css/main.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link
            href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css"
            rel="stylesheet" id="bootstrap-css">
    <script src="https://kit.fontawesome.com/0533c22dcd.js" crossorigin="anonymous"></script>
    <script
            src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body class="app sidebar-mini rtl">
<!-- Navbar-->
<header class="app-header">
    <a class="app-header__logo" href="#" style="margin-top: 10px;"><img
                src="miniorange/sso/resources/images/logo-home.png"></a>
    <!-- Sidebar toggle button<a class="app-sidebar__toggle" href="#" data-toggle="sidebar" aria-label="Hide Sidebar"></a> -->
    <ul class="app-nav">
        <li class="dropdown"><a class="app-nav__item" href="#"
                                data-toggle="dropdown" aria-label="Open Profile Menu"><i class="fa-solid fa-circle-user"></i>
                            <span><?php echo DB::get_registered_user()->email; ?></span>
                            <span><i class="fa fa-caret-down"></i></span></i></a>
            <ul class="dropdown-menu settings-menu dropdown-menu-right">
                <li><a class="dropdown-item" href="mo_oauth_admin_logout.php"><i
                                class="fa fa-sign-out fa-lg"></i> Logout</a></li>
            </ul>
        </li>
    </ul>
</header>
<!-- Sidebar menu-->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>

<aside class="app-sidebar">
    <div class="app-sidebar__user">
        <img src="miniorange/sso/resources/images/miniorange.png"
             style="width: 37.25px; height: 50px;" alt="User Image">
        <div style="margin-left: 15px;">
            <p class="app-sidebar__user-name">Laravel OAuth SSO</p>
            <p class="app-sidebar__user-designation">Package</p>
        </div>
    </div>
    <ul id="app-menu" class="app-menu">
        <li><a class="app-menu__item active" href="mo_oauth_setup.php"><i class="app-menu__icon fa fa-gear"></i><span
                        class="app-menu__label"><b>Plugin Settings</b></span></a></li>
        <li><a class="app-menu__item" href="mo_oauth_how_to_setup.php"><i class="app-menu__icon fa fa-info-circle"></i><span
                        class="app-menu__label"><b>How to Setup?</b></span></a></li>
        <li><a class="app-menu__item" href="mo_oauth_account.php"><i class="app-menu__icon fa-solid fa-user"></i></i><span
                        class="app-menu__label"><b>Account Setup</b></span></a></li>
        <li><a class="app-menu__item" href="mo_oauth_support.php"><i class="app-menu__icon fa-solid fa-headset"></i></i></i><span
                        class="app-menu__label"><b>Support</b></span></a></li>
    </ul>
</aside>
<script>
    jQuery(function($) {
        var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
        $('ul li a').each(function() {
            if (this.href === path) {
                $('ul li a').removeClass("active");
                $(this).addClass('active');
            }
        });
    });
</script>