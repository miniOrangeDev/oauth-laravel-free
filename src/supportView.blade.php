<?php use MiniOrange\Helper\DB;
use MiniOrange\Helper\CustomerDetails as CD;?>
<?php echo View::make('mooauth::menuView'); 
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>
                <i class="fa fa-support"></i> Support/Contact Us
            </h1>

        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Support/Contact Us</a></li>
        </ul>
    </div>

    <p id="oauth_message"></p>

    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <div class="row">
                    <div class="col-lg-10">
                        <form method="post" action="">
                            <p>
                                <b>Need any help? We can help you in configuring the connector
                                    with your Identity Provider. Just send us a query and we will
                                    get back to you soon.</b>
                            </p>
                            <input type="hidden" name="option"
                                   value="mo_oauth_contact_us_query_option"/>
                            <div class="form-group">
                                <input class="form-control" type="email"
                                       name="mo_oauth_contact_us_email" placeholder="Enter your email"
                                       required
                                       value="<?php echo CD::get_option('mo_oauth_admin_email');?>">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="tel"
                                       name="mo_oauth_contact_us_phone" required
                                       pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}"
                                       placeholder="We call only if you do. 	( eg.+1 9876543210, +91 1234567890 )">
                            </div>
                            <div class="form-group">
								<textarea class="form-control" name="mo_oauth_contact_us_query"
                                          required placeholder="Enter your query here"
                                          onkeypress="mo_oauth_valid_query(this)"
                                          onkeyup="mo_oauth_valid_query(this)"
                                          onblur="mo_oauth_valid_query(this)"></textarea>
                            </div>

                    </div>
                </div>
                <div class="tile-footer">
                    <button class="btn btn-primary" type="submit" name="submit">Submit</button>
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