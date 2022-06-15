<?php if (!isset($_SESSION)) session_start();?>
<?php echo View::make('mooauth::menuView'); 
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-user"></i>Account Setup
            </h1>

        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Account Setup</a></li>
        </ul>
    </div>

    <p id="oauth_message"></p>
    <?php
    use MiniOrange\Helper\DB;
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-12">
                        <?php
                        if (mo_oauth_is_customer_registered()) {
                            if (mo_oauth_is_customer_license_verified()) {
                                mo_oauth_show_customer_details();
                            } else {
                                mo_oauth_show_verify_license_page();
                            }
                        } else {
                            mo_oauth_show_verify_password_page();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
<?php
use MiniOrange\Helper\DB as setupDB;

if (isset($_SESSION['show_success_msg'])) {

    echo '<script>
    var message = document.getElementById("oauth_message");
    message.classList.add("success-message");
    message.innerText = "' . DB::get_option('mo_oauth_message') . '";
    </script>';
    unset($_SESSION['show_success_msg']);
    exit();
}
if (isset($_SESSION['show_error_msg'])) {
    echo '<script>
    var message = document.getElementById("oauth_message");
    message.classList.add("error-message");
    message.innerText = "' . DB::get_option('mo_oauth_message') . '";
    message.overflow = "break-word";
    </script>';
    unset($_SESSION['show_error_msg']);
}
?>