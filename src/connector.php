<?php

use MiniOrange\Helper\OauthDB as DB;
use MiniOrange\Helper\CustomerDetails as CD;
use MiniOrange\Helper\Lib\OauthAESEncryption;
use Illuminate\Support\Facades\Schema;
use MiniOrange\Helper\OauthConstants;
use MiniOrange\Classes\Actions\MoOauthDatabaseController as DBinstaller;

if (!defined('MSSP_VERSION'))
    define('MSSP_VERSION', '1.0.0');
if (!defined('MSSP_NAME'))
    define('MSSP_NAME', basename(__DIR__));
if (!defined('MSSP_DIR'))
    define('MSSP_DIR', __DIR__);
if (!defined('MSSP_TEST_MODE'))
    define('MSSP_TEST_MODE', FALSE);

// recursive function to copy files within directory
function mo_oauth_recurse_copy($src, $dst)
{
    $dir = opendir($src);

    @mkdir($dst, 0777, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                mo_oauth_recurse_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                if('sp-key.key' !== $file && 'miniorange_sp_priv_key.key' !== $file)
                    copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

function mo_oauth_register_action()
{
    $email = $_POST['email'];
    $password = stripslashes($_POST['password']);
    $confirm_password = stripslashes($_POST['confirm_password']);
    $use_case = $_POST['use_case'];

    CD::update_option('mo_oauth_admin_email', $email);
    CD::update_option('mo_oauth_use_case', $use_case);
    if (strcmp($password, $confirm_password) == 0) {
        CD::update_option('mo_oauth_admin_password', $password);
        $customer = new Customeroauth();
        $content = json_decode($customer->check_customer(), true);
        $response = mo_oauth_create_customer();
        if (strcasecmp($response['status'], 'success') == 0) {
                $customer->submit_register_user($email, $use_case);
                CD::update_option('mo_oauth_message', 'Registration Successful');
                mo_oauth_show_success_message();
        } else {
            CD::update_option('mo_oauth_admin_email', '');
            CD::update_option('mo_oauth_use_case', '');
        }
    } else {
        $response['status'] = "not_match";
        CD::update_option('mo_oauth_message', 'Passwords do not match.');
        mo_oauth_show_error_message();
    }
}

function mo_oauth_create_customer()
{
    $customer = new Customeroauth();
    $customerKey = json_decode($customer->create_customer(), true);
    $response = array();

    if (strcasecmp($customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0) {
        $api_response = mo_oauth_get_current_customer();
        if ($api_response) {
            $response['status'] = "success";
        } else
            $response['status'] = "error";
    } else if (strcasecmp($customerKey['status'], 'SUCCESS') == 0) {
        CD::update_option('mo_oauth_admin_customer_key', $customerKey['id']);
        CD::update_option('mo_oauth_admin_api_key', $customerKey['apiKey']);
        CD::update_option('mo_oauth_customer_token', $customerKey['token']);
        CD::update_option('mo_oauth_admin_password', '');
        CD::update_option('mo_oauth_message', 'Thank you for registering with miniorange.');
        CD::update_option('mo_oauth_registration_status', '');
        CD::update_option('mo_oauth_use_case', '');
        CD::delete_option('mo_oauth_verify_customer');
        CD::delete_option('mo_oauth_new_registration');
        $response['status'] = "success";
        return $response;
    } else{
        CD::update_option('mo_oauth_message', $customerKey['status']);
        $response['status'] = "error";
    }

    CD::update_option('mo_oauth_admin_password', '');
    return $response;
}

function mo_oauth_get_current_customer()
{
    $customer = new Customeroauth();
    $content = $customer->get_customer_key();
    
    if(strcasecmp($content, 'Invalid username or password. Please try again.') == 0){
        CD::update_option('mo_oauth_message', $content);
        $_SESSION['show_error_msg'] = true;
        $response['status'] = "error";
    } else{
        $customerKey = json_decode($content, true);
        $response = array();
        if (json_last_error() == JSON_ERROR_NONE) {
            CD::update_option('mo_oauth_admin_customer_key', $customerKey['id']);
            CD::update_option('mo_oauth_admin_api_key', $customerKey['apiKey']);
            CD::update_option('mo_oauth_customer_token', $customerKey['token']);
            CD::update_option('mo_oauth_admin_password', '');
            CD::update_option('mo_oauth_use_case', '');
            CD::delete_option('mo_oauth_verify_customer');
            CD::delete_option('mo_oauth_new_registration');
            $response['status'] = "success";
            return $response;
        } else {
    
            CD::update_option('mo_oauth_message', 'You already have an account with miniOrange. Please enter a valid password.');
            mo_oauth_show_error_message();
            $response['status'] = "error";
            return $response;
        }
    }
}

function mo_oauth_show_customer_details()
{
    ?>
    <div class="mo_oauth_table_layout">
        <h2>Thank you for registering with miniOrange.</h2>

        <table id="customer_details_table" border="1" style="background-color: #FFFFFF; border: 1px solid #CCCCCC; border-collapse: collapse; padding: 0px 0px 0px 10px; margin: 2px; width: 85%">
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
        <form style="display: none;" id="loginform"
              action="<?php echo DB::get_option('mo_oauth_host_name') . 'moas/login'; ?>"
              target="_blank" method="post">
            <input type="email" name="username"
                   value="<?php echo CD::get_option('mo_oauth_admin_email'); ?>"/> <input
                    type="text" name="redirectUrl"
                    value="<?php echo DB::get_option('mo_oauth_host_name') . 'moas/initializepayment'; ?>"/>
            <input type="text" name="requestOrigin" id="requestOrigin"/>
        </form>
        <script>
            function upgradeform(planType) {
                jQuery('#requestOrigin').val(planType);
                    jQuery('#loginform').submit();
            }
        </script>
    </div>
    <?php
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

function mo_oauth_remove_account()
{
    CD::delete_option('mo_oauth_admin_customer_key');
    CD::delete_option('mo_oauth_admin_api_key');
    CD::delete_option('mo_oauth_customer_token');
    CD::delete_option('mo_oauth_admin_email');
    CD::delete_option('mo_oauth_registration_status');
}

function moOauthcheckPasswordpattern($password)
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

function mo_oauth_is_guest_enabled()
{
    $guest_enabled = DB::get_option('mo_oauth_guest_enabled');
    return $guest_enabled;
}

function check_license()
{
    $code = DB::get_option('sml_lk');
    if ($code) {
        $code = OauthAESEncryption::decrypt_data($code, $key);
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
        if (OauthAESEncryption::decrypt_data(DB::get_option('site_ck_l'), $key) == "true")
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
            $code = OauthAESEncryption::decrypt_data($code, $key);
            $customer = new Customeroauth();
            $content = json_decode($customer->mo_oauth_vl($code, true), true);
            if (strcasecmp($content['status'], 'SUCCESS') == 0) {
                DB::delete_option('vl_check_s');
            } else {
                DB::update_option('vl_check_s', OauthAESEncryption::encrypt_data("false", $key));
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
        $vl_check_s = OauthAESEncryption::decrypt_data($vl_check_s, $key);
        if ($vl_check_s == "false")
            return true;
    }
    return false;
}

function mo_oauth_is_user_registered()
{
    return DB::get_registered_user();
}

function mo_oauth_sanitize_certificate($certificate)
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
