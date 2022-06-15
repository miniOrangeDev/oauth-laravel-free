<?php use MiniOrange\Helper\DB;?>
<?php echo View::make('mooauth::menuView'); 
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-info-circle"></i> How to Setup?
            </h1>

        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">How to Setup?</a></li>
        </ul>
    </div>
    <p id="oauth_message"></p>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-10">
                        <h3>Follow these steps to setup the plugin:</h3>
                        <h4>Step 1:</h4>
                        <ul>
                            <li>You can configure the SP Base URL or leave this option as it is.</li>
                            <li>You need to provide these <b>SP Entity ID</b> and <b>ACS URL</b>
                                values while configuring your
                                Identity Provider
                            </li>
                    
                        </ul>
                        <img src="miniorange/sso/resources/images/setup_2.png"
                             style="width: 800px; height: 380px; margin-left: 50px; border: 1px solid;">
                        <br/> <br/>
                        <h4>Step 2:</h4>
                        <ul>
                            <li>Use your Identity Provider details to
                            configure the plugin.</li>
                        </ul>
                        <img src="miniorange/sso/resources/images/setup_1.png"
                             style="width: 800px; height: 380px; margin-left: 50px; border: 1px solid;"><br/><br/>
                        <ul>
                            <li>Click on the <b>Save</b> button to save your configuration.</li>
                        </ul>     
                        
                        <h4>Step 3:</h4>
                        <ul>
                            <li>You can test if the plugin is configured properly or not by
                                clicking on the <b>Test Configuration</b> button.
                            </li>
                        </ul>
                        <img src="miniorange/sso/resources/images/setup_3.png"
                             style="width: 800px; height: 380px; margin-left: 50px; border: 1px solid;">
                        <ul>
                            <br/>
                            <li>If the configuration is correct, you should see a Test
                                Successful screen with the user's attribute values.
                            </li>
                        </ul>
                        <img src="miniorange/sso/resources/images/setup_4.png"
                             style="width: 600px; height: 400px; margin-left: 50px; border: 1px solid;">
                        <br/><br/>
                        <h4>Step 4:</h4>
                        <ul>
                            <li>Your users can initiate the Single Sign On flow by clicking on the login button generated on your login page. If you do not have this page yet, run <i>php artisan make:auth</i> & <i>php artisan migrate</i> to generate the authentication module.
                            </li>
                        </ul>
                        <img src="miniorange/sso/resources/images/setup_5.png"
                             style="width: 800px; height: 380px; margin-left: 50px; border: 1px solid;">
                        <br/><br/>
                        <ul>
                            <li>You can create your own Single Sign On link. Make sure it redirects you to the SSO link : <b><?php echo $_SERVER['HTTP_HOST'].'/login.php';?></b>. Refer to the example given below : </li>
                            <code style="font-size: 17px">

                                        &lta href="login.php"&gtSingle Sign On&lt/a&gt

                            </code>
                        </ul>
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