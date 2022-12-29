<?php use MiniOrange\Helper\OauthDB;
use MiniOrange\Helper\CustomerDetails as CD;?>
<?php echo View::make('mooauth::menuView'); 
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>Trial/Demo Request</h1>
        </div>
    </div>

    <p id="oauth_message"></p>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" action="">
                            <p>
                                <b>Need trial/demo to test the premium features before purchasing the plan? Just send us a request and we will get back to you soon.</b>
                            </p>
                            <input type="hidden" name="option"
                                   value="mo_oauth_trial_request_option"/>
                            <div class="form-group">
                                <input class="form-control" type="email"
                                       name="mo_oauth_trial_request_email" placeholder="Enter your email"
                                       required
                                       value="<?php echo CD::get_option('mo_oauth_admin_email');?>">
                            </div>
                            <div class="form-group">
								<textarea class="form-control" name="mo_oauth_trial_request_use_case"
                                          required placeholder="What's your use case?"
                                          onkeypress="mo_oauth_valid_query(this)"
                                          onkeyup="mo_oauth_valid_query(this)"
                                          onblur="mo_oauth_valid_query(this)"></textarea>
                            </div>

                    </div>
                </div>
                <div class="tile-footer">
                    <button class="btn btn-primary" type="submit" name="submit" style="margin-left: 45%;">Submit</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
    function mo_oauth_valid_query(f) {
        !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
            /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
    }
</script>
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