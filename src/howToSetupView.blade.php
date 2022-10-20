<?php use MiniOrange\Helper\DB;?>
<?php echo View::make('mooauth::menuView'); 
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>How to Setup?</h1>
        </div>
    </div>
    <p id="oauth_message"></p>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-12">
                        <h3>Follow these steps to setup the plugin:</h3>
                        <h4>Step 1:</h4>
                        <ul>
                            <li>You can choose an OAuth Provider from the dropdown (if your OAuth Provider is not mentioned, you can opt for Other in the list).</li>
                            <li>You need to provide the <b>Redirect/Callback URL</b>
                                while configuring your
                                OAuth Provider.
                            </li>
                    
                        </ul>
                        <img src="miniorange/sso/resources/images/Step_1.png"
                             style="width: 800px; height: 380px; margin-left: 50px; border: 1px solid;">
                        <br/> <br/>
                        <h4>Step 2:</h4>
                        <ul>
                            <li>Use your OAuth Provider details like <b>Client ID </b> and <b>Client Secret</b> to
                            configure the plugin.</li>
                            <li>After that, you can enter the <b>Scope</b>, <b>Authorization Endpoint</b>, <b>Access Token Endpoint</b>, <b> GetUserinfo Endpoint</b>,
                            <b>Realm</b>, <b>Domain</b>, <b>Tenant</b>, (as per your OAuth Provider or use the default ones provided already) </li>
                            <li>You can send the client credentials in header or body and also send state parameter accordingly.</li>
                        </ul>
                        <img src="miniorange/sso/resources/images/Step_2.png"
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
                        <img src="miniorange/sso/resources/images/Step_3.png"
                             style="width: 800px; height: 380px; margin-left: 50px; border: 1px solid;">
                        <ul>
                            <br/>
                            <li>If the configuration is correct, you should see a Test
                                Successful screen with the user's attribute values.
                            </li>
                        </ul>
                        <img src="miniorange/sso/resources/images/Step_4.png"
                             style="width: 600px; height: 600px; margin-left: 50px; border: 1px solid;">
                        <br/><br/>
                        <h4>Step 4:</h4>
                        <ul>
                            <li>Your users can initiate the Single Sign On flow by using <b><?php echo $_SERVER['HTTP_HOST'].'/ssologin.php?option=oauthredirect';?></b>
                            </li>
                        </ul>
                        <ul>
                            <li>You can create your own Single Sign On link. Make sure it redirects you to the SSO link : <b><?php echo $_SERVER['HTTP_HOST'].'/ssologin.php?option=oauthredirect';?></b>. Refer to the example given below : </li>
                            <code style="font-size: 17px">

                                        &lta href="ssologin.php?option=oauthredirect"&gtSingle Sign On&lt/a&gt

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