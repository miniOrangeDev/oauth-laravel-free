<?php if (!isset($_SESSION)) session_start();?>
<?php echo View::make('mooauth::menuView'); 
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>Account Setup</h1>
        </div>
    </div>

    <p id="oauth_message"></p>
    <?php
    use MiniOrange\Helper\OauthDB;
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-10">
                        <input type="hidden" value="" id="mo_customer_registered">
                        <div id="pricing_container">
                            <h2>Licensing Plans</h2>
                            <br/>
                            <div style="display: -webkit-inline-box; display: -moz-inline-box; margin-left:300px; width:300px!important;">
                                <div class="thumbnail" style="margin-left:-320px; margin-right:10px;">
                                    <div class="mo-tab">
                                        <h3>Free</h3>
                                        <br/><br/>
                                        <!-- <a class="btn btn-primary btn-large" href="downloads/php-oauth-single-sign-on-trial.zip" target="_blank">Download</a> -->
                                        <hr>

                                        <h4 style="padding-top:40px;margin-bottom:45px;">$0</h4>
                                        <hr>
                                        <p>Unlimited SSO Authentications</p>
                                        <p>Just In Time User Provisioning / Auto-creation</p>
                                        <p>Account Linking</p>
                                        <p>Basic Attribute Mapping</p>
                                        <p>&nbsp</p>
                                        <p>&nbsp</p>
                                        <p>&nbsp</p>
                                        <p>&nbsp</p>
                                        <p>&nbsp</p>
                                        <p>&nbsp</p>
                                        <hr style="margin-top: 88px;">
                                        <h4>Basic Email Support</h4>
                                        <br/>
                                    </div>
                                </div>
                                <div class="thumbnail" style="margin-right:10px">
                                    <div class="mo-tab">
                                        <h3>Premium</h3>
                                        <br>
                                        <br>
                                        <hr>
                                        <script>

                                            let anchorlinks = document.querySelectorAll('a[href^="#"]')

                                            for (let item of anchorlinks) {
                                                item.addEventListener('click', (e) => {
                                                    let hashval = item.getAttribute('href')
                                                    let target = document.querySelector(hashval)
                                                    target.scrollIntoView({
                                                        behavior: 'smooth',
                                                        block: 'start'
                                                    })
                                                    history.pushState(null, null, hashval)
                                                    e.preventDefault()
                                                })
                                            }
                                        </script>
                                        
                                        <h4>$549</h4>
                                        <a class="btn btn-large" id="upgrade_button" href="#" onclick="upgradeform('laravel_oauth_premium_plan')">Upgrade</a>
                                        <br>
                                        <hr>
                                        <p>Unlimited SSO Authentications</p>
                                        <p>Auto Create unlimited users</p>
                                        <p>Just In Time User Provisioning / Auto-creation</p>
                                        <p>Account Linking</p>
                                        <p>Advanced Attribute Mapping</p>
                                        <p>Custom Attribute Mapping</p>
                                        <p>OAuth/OpenID Supported Grant Types</p>
                                        <p>Redirect all SSO users to specific URL after Login or Logout</p>
                                        <p>JWT Vadilation Support</p>
                                        <p>Protect Complete Site with SSO</p>
                                        <p>Domain Restrictions</p>
                                        <hr>
                                        <h4>Support plans on demand</h4>
                                        <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div id="customer">
                        <?php
                            mo_oauth_show_customer_details();
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
<?php
use MiniOrange\Helper\OauthDB as setupDB;

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