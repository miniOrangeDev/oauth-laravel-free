<?php

use MiniOrange\Helper\DB;
use MiniOrange\Helper\CustomerDetails as CD;
use MiniOrange\Helper\Lib\AESEncryption;
use Illuminate\Support\Facades\Schema;
use MiniOrange\Helper\Constants;
use MiniOrange\Classes\Actions\DatabaseController as DBinstaller;

if (!defined('MSSP_VERSION'))
    define('MSSP_VERSION', '1.0.0');
if (!defined('MSSP_NAME'))
    define('MSSP_NAME', basename(__DIR__));
if (!defined('MSSP_DIR'))
    define('MSSP_DIR', __DIR__);
if (!defined('MSSP_TEST_MODE'))
    define('MSSP_TEST_MODE', FALSE);

// recursive function to copy files within directory
function recurse_copy($src, $dst)
{
    $dir = opendir($src);

    @mkdir($dst, 0777, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                if('sp-key.key' !== $file && 'miniorange_sp_priv_key.key' !== $file)
                    copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function mo_register_action()
{

    $email = $_POST['email'];
    $password = stripslashes($_POST['password']);
    $confirmPassword = stripslashes($_POST['confirmPassword']);

    DB::update_option('mo_oauth_admin_email', $email);
    if (strcmp($password, $confirmPassword) == 0) {
        DB::update_option('mo_oauth_admin_password', $password);
        $customer = new Customeroauth();
        $content = json_decode($customer->check_customer(), true);
        if (strcasecmp($content['status'], 'CUSTOMER_NOT_FOUND') == 0) {

            $response = create_customer();
        } else {
            $response = get_current_customer();
        }
        DB::update_option('mo_oauth_message', 'Logged in as Guest.');
        mo_oauth_show_success_message();
    } else {
        $response['status'] = "not_match";
        DB::update_option('mo_oauth_message', 'Passwords do not match.');
        mo_oauth_show_error_message();
    }
}

function create_customer()
{
    $customer = new Customeroauth();
    $customerKey = json_decode($customer->create_customer(), true);
    $response = array();
    if (strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {
        $api_response = get_current_customer();
        if ($api_response) {
            $response['status'] = "success";
        } else
            $response['status'] = "error";
    } else if (strcasecmp($customerKey['status'], 'SUCCESS') == 0) {
        DB::update_option('mo_oauth_admin_customer_key', $customerKey['id']);
        DB::update_option('mo_oauth_admin_api_key', $customerKey['apiKey']);
        DB::update_option('mo_oauth_customer_token', $customerKey['token']);
        DB::update_option('mo_oauth_free_version', 1);
        DB::update_option('mo_oauth_admin_password', '');
        DB::update_option('mo_oauth_message', 'Thank you for registering with miniorange.');
        DB::update_option('mo_oauth_registration_status', '');
        DB::delete_option('mo_oauth_verify_customer');
        DB::delete_option('mo_oauth_new_registration');
        $response['status'] = "success";
        return $response;
    }

    DB::update_option('mo_oauth_admin_password', '');
    return $response;
}

function get_current_customer()
{
    $customer = new Customeroauth();
    $content = $customer->get_customer_key();

    $customerKey = json_decode($content, true);

    $response = array();
    if (json_last_error() == JSON_ERROR_NONE) {
        DB::update_option('mo_oauth_admin_customer_key', $customerKey['id']);
        DB::update_option('mo_oauth_admin_api_key', $customerKey['apiKey']);
        DB::update_option('mo_oauth_customer_token', $customerKey['token']);
        DB::update_option('mo_oauth_admin_password', '');
        $certificate = DB::get_option('oauth_x509_certificate');
        if (empty($certificate)) {
            DB::update_option('mo_oauth_free_version', 1);
        }

        DB::delete_option('mo_oauth_verify_customer');
        DB::delete_option('mo_oauth_new_registration');
        $response['status'] = "success";
        return $response;
    } else {

        DB::update_option('mo_oauth_message', 'You already have an account with miniOrange. Please enter a valid password.');
        mo_oauth_show_error_message();
        $response['status'] = "error";
        return $response;
    }
}

function mo_oauth_show_success_message()
{
    if (isset($_SESSION['show_error_msg']))
        unset($_SESSION['show_error_msg']);
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['show_success_msg'] = 1;
}

function mo_oauth_show_error_message()
{
    if (isset($_SESSION['show_success_msg']))
        unset($_SESSION['show_success_msg']);
    if (!isset($_SESSION)) {
        session_start();
    }
    $_SESSION['show_error_msg'] = 1;
}

function mo_oauth_check_empty_or_null($value)
{
    if (!isset($value) || empty($value)) {
        return true;
    }
    return false;
}

function mo_oauth_is_customer_registered()
{
    $email = CD::get_option('mo_oauth_admin_email');
    $customerKey = CD::get_option('mo_oauth_admin_customer_key');
    if (!$email || !$customerKey || !is_numeric(trim($customerKey))) {
        return false;
    } else {
        return true;
    }
}

function mo_oauth_is_customer_license_verified()
{
    $status = CD::get_option('mo_oauth_registration_status');
    if ($status != 'verified')
        return false;
    else
        return true;
}

function mo_oauth_show_verify_password_page()
{
    ?>
    <form name="f" method="post" action="">
        <input type="hidden" name="option" value="mo_oauth_verify_customer">
         <input type="hidden" name="_token" value="<?php echo csrf_token() ?>" />
        <div class="mo_oauth_table_layout">
            <div id="toggle1" class="panel_toggle">
                <h3>Login with miniOrange</h3>
            </div>
            <div id="panel1">
                <p>
                    <b>Please enter your miniOrange email and password.<br/> <a
                                target="_blank"
                                href="https://login.xecurify.com/moas/idp/resetpassword">Click
                            here if you forgot your password?</a></b>
                </p>
                <br/>
                <div class="col-lg-8">
                    <table class="mo_oauth_settings_table">
                        <tr>
                            <td><b><font color="#FF0000">*</font>Email:</b></td>
                            <td><input class="form-control" type="email" name="email" required
                                       placeholder="person@example.com"
                                       value="<?php echo CD::get_option('mo_oauth_admin_email'); ?>"/></td>
                        </tr>
                        <tr>
                            <td><b><font color="#FF0000">*</font>Password:</b></td>
                            <td><input class="form-control" required type="password"
                                       name="password" placeholder="Enter your password" minlength="6"
                                       pattern="^[(\w)*(!@#$.%^&*-_)*]+$"
                                       title="Minimum 6 characters should be present. Maximum 15 characters should be present. Only following symbols (!@#.$%^&*) should be present."/>
                            </td>
                        </tr>
                            <td>&nbsp;</td>
                            <td><input type="submit" name="submit" value="Login"
                                       class="btn btn-primary"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <!-- <input type="button" name="mo_oauth_goback" id="mo_oauth_goback" value="Back"
                             class="btn btn-primary"/> -->

                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </form>

    <!-- <form name="f" method="post" action="" id="mo_oauth_goback_form">
                <input type="hidden" name="option" value="mo_oauth_go_back"/>
            </form> -->
    <form name="f" method="post" action="" id="mo_oauth_forgotpassword_form">
        <input type="hidden" name="option"
               value="mo_oauth_forgot_password_form_option"/>
    </form>
    <script>
        // jQuery("#mo_oauth_goback").click(function () {
        // jQuery("#mo_oauth_goback_form").submit();
        // });
        jQuery("a[href=\"#mo_oauth_forgot_password_link\"]").click(function () {
            jQuery("#mo_oauth_forgotpassword_form").submit();
        });
    </script>
    <?php
}

function mo_oauth_show_customer_details()
{
    ?>
    <div class="mo_oauth_table_layout">
        <h2>Thank you for registering with miniOrange.</h2>

        <table border="1"
               style="background-color: #FFFFFF; border: 1px solid #CCCCCC; border-collapse: collapse; padding: 0px 0px 0px 10px; margin: 2px; width: 85%">
            <tr>
                <td style="width: 45%; padding: 10px;">miniOrange Account Email</td>
                <td style="width: 55%; padding: 10px;"><?php echo CD::get_option('mo_oauth_admin_email'); ?></td>
            </tr>
            <tr>
                <td style="width: 45%; padding: 10px;">Customer ID</td>
                <td style="width: 55%; padding: 10px;"><?php echo CD::get_option('mo_oauth_admin_customer_key') ?></td>
            </tr>
        </table>
        <br/> <br/>

        <table>
            <tr>
                <td>
                    <form name="f1" method="post" action="" id="mo_oauth_goto_login_form"
                          style="margin-block-end: auto;">
                        <input type="hidden" value="change_miniorange" name="option"/> <input
                                type="submit" value="Remove Account and Release License" class="btn btn-primary"/>
                    </form>
                </td>
            </tr>
        </table>

        <br/>

        <form style="display: none;" id="loginform"
              action="<?php echo DB::get_option('mo_oauth_host_name') . '/moas/login'; ?>"
              target="_blank" method="post">
            <input type="email" name="username"
                   value="<?php echo CD::get_option('mo_oauth_admin_email'); ?>"/> <input
                    type="text" name="redirectUrl"
                    value="<?php echo DB::get_option('mo_oauth_host_name') . '/moas/initializepayment'; ?>"/>
            <input type="text" name="requestOrigin" id="requestOrigin"/>
        </form>
    </div>
    <?php
}

function mo_oauth_show_verify_license_page()
{
    ?>
    <div class="mo_oauth_table_layout"
         style="padding-bottom: 50px;!important">

        <h3>
            Verify License [ <span style="font-size: 13px; font-style: normal;"><a
                        style="cursor: pointer;" onclick="getlicensekeysform()">Click here to
				view your license key</a></span> ]
        </h3>
        <hr>

        <?php
        echo '<form style="display:none;" id="loginform" action="' . DB::get_option('mo_oauth_host_name') . '/moas/login"
                            target="_blank" method="post">
                            <input type="email" name="username" value="' . CD::get_option('mo_oauth_admin_email') . '" />
                            <input type="text" name="redirectUrl" value="' . DB::get_option('mo_oauth_host_name') . '/moas/viewlicensekeys" />
                             <input type="hidden" name="_token" value="'. csrf_token().'" />
                            <input type="text" name="requestOrigin" value="wp_oauth_sso_basic_plan"  />
                        </form>';
        ?>

        <form name="f" method="post" action="">
            <input type="hidden" name="option" value="mo_oauth_verify_license"/>
             <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>" />
            <div class="form-group">
                <label for="oauth_license_key"><b><font color="#FF0000">*</font>Enter
                        your license key to activate the connector:</b></label> <input
                        class="form-control" required type="text"
                        style="margin-left: 40px; width: 300px;" name="oauth_license_key"
                        id="oauth_license_key"
                        placeholder="Enter your license key to activate the connector"/>
            </div>

            <ol>
                <li>License key you have entered here is associated with this site
                    instance. In future, if you are re-installing the connector or your
                    site for any reason, you should logout of your miniOrange account
                    from the connector and then delete the connector. So that you can
                    reuse the same license key.
                </li>
                <br>
                <li><b>This is not a developer's license.</b> Making any kind of
                    change to the connector's code will delete all your configuration
                    and make the connector unusable.
                </li>
                <br>
                <div class="animated-checkbox">
                    <label> <input type="checkbox" name="license_conditions"
                                   id="license_conditions" required> <span class="label-text"><strong>I
							have read the above two conditions and I want to activate the
							connector.</strong></span>
                    </label>
                </div>
            </ol>
            <input type="submit" name="submit" value="Activate License"
                   class="btn btn-primary"/> <input type="button" name="mo_oauth_goback"
                                                    id="mo_oauth_goback" value="Back" class="btn btn-primary"/>

        </form>
    </div>

    <form name="f" method="post" action="" id="mo_oauth_free_trial_form">
        <input type="hidden" name="option" value="mo_oauth_free_trial"/>
    </form>
    <form name="f" method="post" action="" id="mo_oauth_check_license">
        <input type="hidden" name="option" value="mo_oauth_check_license"/>
    </form>
    <form name="f" method="post" action="" id="mo_oauth_goback_form">
        <input type="hidden" name="option" value="mo_oauth_go_back"/>
    </form>
    <script>
        jQuery("#mo_oauth_free_trial_link").click(function () {
            jQuery("#mo_oauth_free_trial_form").submit();
        });
        jQuery("a[href=\"#checklicense\"]").click(function () {
            jQuery("#mo_oauth_check_license").submit();
        });
        jQuery("#mo_oauth_goback").click(function () {
            jQuery("#mo_oauth_goback_form").submit();
        });
    </script>
    <?php
}

function mo_oauth_remove_account()
{
    CD::delete_option('mo_oauth_admin_customer_key');
    CD::delete_option('mo_oauth_admin_api_key');
    CD::delete_option('mo_oauth_customer_token');
    CD::delete_option('mo_oauth_admin_email');
    CD::delete_option('mo_oauth_registration_status');
}

function checkPasswordpattern($password)
{
    $pattern = '/^[(\w)*(\!\@\#\$\%\^\&\*\.\-\_)*]+$/';

    return !preg_match($pattern, $password);
}

function mo_oauth_is_curl_installed()
{
    if (in_array('curl', get_loaded_extensions())) {
        return 1;
    } else {
        return 0;
    }
}

function mo_oauth_is_customer_registered_oauth($check_guest = true)
{
    $email = DB::get_option('mo_oauth_admin_email');
    $customerKey = DB::get_option('mo_oauth_admin_customer_key');

    if (mo_oauth_is_guest_enabled() && $check_guest)
        return 1;
    if (!$email || !$customerKey || !is_numeric(trim($customerKey))) {
        return 0;
    } else {
        return 1;
    }
}

function mo_oauth_is_guest_enabled()
{
    $guest_enabled = DB::get_option('mo_oauth_guest_enabled');
    return $guest_enabled;
}

function check_license()
{
    $code = DB::get_option('sml_lk');
    if ($code) {
        $code = AESEncryption::decrypt_data($code, $key);
        $customer = new Customeroauth();
        $content = json_decode($customer->mo_oauth_vl($code, true), true);
        if (strcasecmp($content['status'], 'SUCCESS') == 0) {
            return true;
        } else {
            return false;
        }
    }
}

function site_check()
{
    $status = false;
    $key = DB::get_option('mo_oauth_customer_token');
    if (DB::get_option("site_ck_l")) {
        if (AESEncryption::decrypt_data(DB::get_option('site_ck_l'), $key) == "true")
            $status = true;
    }
    if ($status && !mo_oauth_lk_multi_host()) {
        $vl_check_t = DB::get_option('vl_check_t');
        if ($vl_check_t) {
            $vl_check_t = intval($vl_check_t);
            if (time() - $vl_check_t < 3600 * 24 * 3)
                return $status;
        }
        $code = DB::get_option('sml_lk');
        if ($code) {
            $code = AESEncryption::decrypt_data($code, $key);
            $customer = new Customeroauth();
            $content = json_decode($customer->mo_oauth_vl($code, true), true);
            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                DB::delete_option('vl_check_s');
            } else {
                DB::update_option('vl_check_s', AESEncryption::encrypt_data("false", $key));
            }
        }
        DB::update_option('vl_check_t', time());
    }
    return $status;
}

function mo_oauth_lk_multi_host()
{
    $vl_check_s = DB::get_option('vl_check_s');
    $key = DB::get_option('mo_oauth_customer_token');
    if ($vl_check_s) {
        $vl_check_s = AESEncryption::decrypt_data($vl_check_s, $key);
        if ($vl_check_s == "false")
            return true;
    }
    return false;
}

function is_user_registered()
{
    return DB::get_registered_user();
}

function sanitize_certificate($certificate)
{
    $certificate = trim($certificate);
    $certificate = preg_replace("/[\r\n]+/", "", $certificate);
    $certificate = str_replace("-", "", $certificate);
    $certificate = str_replace("BEGIN CERTIFICATE", "", $certificate);
    $certificate = str_replace("END CERTIFICATE", "", $certificate);
    $certificate = str_replace(" ", "", $certificate);
    $certificate = chunk_split($certificate, 64, "\r\n");
    $certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
    return $certificate;
}

?>
