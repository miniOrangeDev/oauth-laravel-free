<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Main CSS-->
    <link rel="stylesheet" type="text/css"
          href="miniorange/sso_oauth_free/includes/css/moOauthMain.css">
    <!-- Font-icon css-->
    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Font Awesome Kit -->
    <script src="https://kit.fontawesome.com/0533c22dcd.js" crossorigin="anonymous"></script>
    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Login - miniOrange Admin</title>
</head>
<body>
<section class="material-half-bg">
    <div class="cover"></div>
</section>
<section class="login-content">
    <div class="logo">
        <h1>
            <img src="miniorange/sso_oauth_free/resources/images/logo_large.png">
        </h1>
    </div>
    <div class="col-md-5">
        <div class="tile">
            <h3 class="tile-title">Login with miniOrange</h3>
            <form class="login_form" method="POST" action="">
                <input type="hidden" name="option" value="admin_login">
                <br/>
                <div class="tile-body">
                    <div class="form-group row">
                        <label class="control-label col-md-3">Email</label>
                        <div class="col-md-8">
                            <input class="form-control col-md-10" type="email" name="email"
                                   placeholder="Enter email address" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-md-3">Password</label>
                        <div class="col-md-8">
                            <input class="form-control col-md-10" type="password"
                                   name="password" id="password" placeholder="Enter your password" required>
                            @csrf
                        </div>
                    </div>
                    <div class="form-group row"  id="use_case_div">
                        <label class="control-label col-md-3">Use case <span style="color: grey">(Optional)</span></label>
                        <div class="col-md-8">
                            <textarea class="form-control col-md-10" id="use_case" name="use_case" rows="4" cols="50" placeholder="Enter your use case here..."></textarea>
                        </div>
                    </div>
                    <a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword"> Click here if you forgot your password?</a>
                </div>
                <div class="tile-footer">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-3">
                            <button class="btn btn-primary" id="login" type="submit"  style="margin-left: 38%;">
                                <i class="fa fa-fw fa-lg fa-check-circle"></i>Login
                            </button>
                            <button type="button" name="mo_oauth_goback" id="goto_register" class="btn btn-primary">
                            <i class="fa fa-user-plus" aria-hidden="true"></i>Register
                            </button>
                        </div>
                    </div>
                </div>
                <script>
                        document.getElementById("goto_register").onclick = function(){
                            window.location.href = "mo_oauth_register.php";
                        }
                        <?php
                            if(mo_oauth_is_user_registered()){
                                ?>
                                document.getElementById("goto_register").style.visibility = 'hidden';
                                document.getElementById("use_case_div").style.display = 'none';
                                <?php
                            }
                        ?>
                </script>
            </form>
        </div>
    </div>
</section>


<!-- Essential javascripts for application to work-->
<script src="miniorange/sso_oauth_free/includes/js/jquery-3.2.1.min.js"></script>
<script src="miniorange/sso_oauth_free/includes/js/popper.min.js"></script>
<script src="miniorange/sso_oauth_free/includes/js/bootstrap.min.js"></script>
<script src="miniorange/sso_oauth_free/includes/js/main.js"></script>
<!-- The javascript plugin to display page loading on top-->
<script src="miniorange/sso_oauth_free/includes/js/plugins/pace.min.js"></script>
<script type="text/javascript"
        src="miniorange/sso_oauth_free/includes/js/plugins/bootstrap-notify.min.js"></script>
<script type="text/javascript"
        src="miniorange/sso_oauth_free/includes/js/plugins/sweetalert.min.js"></script>
<?php
if (isset($_SESSION['invalid_credentials']) && !empty($_SESSION['invalid_credentials'])) {
    if ($_SESSION['invalid_credentials'] === true) {
        echo '<script>
                $(document).ready(function(){
                $.notify({
                    title: "ERROR: ",
                    message: "Invalid username or password",
                    icon: \'fa fa-times\' 
                },{
                    type: "danger"
                });
            });
            </script>';
        unset($_SESSION['invalid_credentials']);
    }
}
?>
</body>
</html>
<?php
use MiniOrange\Helper\OauthDB as setupDB;
if (isset($_SESSION['show_success_msg'])) {

    echo '<script>
    var message = document.getElementById("oauth_message");
    message.classList.add("success-message");
    message.innerText = "' . setupDB::get_option('mo_oauth_message') . '"
    </script>';
    unset($_SESSION['show_success_msg']);
    exit();
}
if (isset($_SESSION['show_error_msg'])) {
    echo '<script>
    var message = document.getElementById("oauth_message");
    message.classList.add("error-message");
    message.innerText = "' . DB::get_option('mo_oauth_message') . '"
    </script>';
    unset($_SESSION['show_error_msg']);
}
?>