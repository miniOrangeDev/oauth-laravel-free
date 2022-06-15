<?php
namespace MiniOrange\Classes\Actions;

use Illuminate\Routing\Controller;
use MiniOrange\Helper\DB;
use MiniOrange\Helper\Mo_OAuth_Hanlder;
use Illuminate\Support\Facades\Session;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MoSSOController extends Controller {
    public function __construct()
    {
        $this->middleware('Illuminate\Session\Middleware\StartSession');
        $this->middleware('web');
    }

    public function launch(Request $request){

                if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'oauthredirect' ) !== false ) {

                    if(isset($_REQUEST['test']))
                        setcookie("mo_oauth_test", true, null, null, null, true, true);
                    else
                        setcookie("mo_oauth_test", false, null, null, null, true, true);

                    $app = json_decode(DB::get_option('mo_oauth_provider_config'),true);
                    
                    if($app == false){
                        exit("Looks like you have not configured OAuth provider, please try to configure OAuth provider first");
                    }

                        if(isset($app['send_state'])!== "true" || $app['send_state'] | $app['appId'] == 'oauth1' || $app['appId'] == 'twitter'){

                            $state = base64_encode($app['displayappname']);
                            $authorizationUrl = $app['authorizeurl'];
                        
                            if(strpos($authorizationUrl, '?' ) !== false)
                            $authorizationUrl = $authorizationUrl."&client_id=".$app['clientid']."&scope=".$app['scope']."&redirect_uri=".$app['redirecturi']."&response_type=code&state=".$state;
                            else
                            $authorizationUrl = $authorizationUrl."?client_id=".$app['clientid']."&scope=".$app['scope']."&redirect_uri=".$app['redirecturi']."&response_type=code&state=".$state;

                            if ( strpos( $authorizationUrl, 'apple' ) !== false ) {
                                $authorizationUrl = str_replace( "response_type=code", "response_type=code+id_token", $authorizationUrl );
                                $authorizationUrl = $authorizationUrl . "&response_mode=form_post";
                            }

                            if(session_id() == '' || !isset($_SESSION))
                                session_start();
                            $_SESSION['oauth2state'] = $state;
                            $_SESSION['appname'] = $app['displayappname'];
                            header('Location: ' . $authorizationUrl);
                            exit;
                        }
                        else{
                            $state=null;
                            $authorizationUrl = $app['authorizeurl'];
                        
                            if(strpos($authorizationUrl, '?' ) !== false)
                            $authorizationUrl = $authorizationUrl."&client_id=".$app['clientid']."&scope=".$app['scope']."&redirect_uri=".$app['redirecturi']."&response_type=code";
                            else
                            $authorizationUrl = $authorizationUrl."?client_id=".$app['clientid']."&scope=".$app['scope']."&redirect_uri=".$app['redirecturi']."&response_type=code";

                            if(session_id() == '' || !isset($_SESSION))
                                session_start();
                            $_SESSION['oauth2state'] = $state; 
                            $_SESSION['appname'] = $app['displayappname'];

                            header('Location: ' . $authorizationUrl);
                            exit;
                        }
                    }
                
                else if( strpos( $_SERVER['REQUEST_URI'], "openidcallback") !== false ||((strpos( $_SERVER['REQUEST_URI'], "oauth_token")!== false)&&(strpos( $_SERVER['REQUEST_URI'], "oauth_verifier") ))) {
                            
                            $currentapp = DB::get_option('mo_oauth_provider_config');
                            $username_attr = $app['username_attr'];
                                    

                            $resourceOwner = MO_Custom_OAuth1::mo_oidc1_get_access_token($_COOKIE['tappname']);

                            $username = "";
                            if(!empty($username_attr))
                                $username = $this->mo_oauth_client_getnestedattribute($resourceOwner, $username_attr); //$resourceOwner[$email_attr];
                            
                            if(empty($username) || "" === $username){
                                exit('Username not received. Check your <b>Attribute Mapping</b> configuration.');
                            }
                            
                            if ( ! is_string( $username ) ) {
                                exit( 'Username is not a string.');
                            }
                    
                            // $this->mo_oauth_login_user($username,$currentapp['redirecturi']);
                            
                            $redirect_to = $currentapp['redirecturi'];
                            $username_attr = $username;
                                    $redirect_to = str_replace('ssologin.php','', $redirect_to);
                            $user = User::where('email', $username_attr)->first();

                            if ($user == null ) { // Create User if not existing
                                $user = new User();
                                $user->email = $username_attr;
                            }

                            $name_column_exists = $user->getConnection()->getSchemaBuilder()->hasColumn($user->getTable(),'name');
                            if($name_column_exists) {
                                $user->name = $username_attr;
                            }
                            $user->password = Hash::make(Str::random(8));

                            try {
                                $user->save();
                            } catch (\PDOException $e) {
                                if ($e->getCode() == '42S22')
                                    echo "<b>" . $e->getCode() . " : Database > Table > Column not found.</b> It seems your <b>Users table</b> does not have column(s) <b>" . implode(", ", array_keys($custom)) . "</b> as mapped in <a href=/setup.php>Custom Attribute Mapping</a>. Please check your <a href=/setup.php>Custom Attribute Mapping</a> and <b>Users table</b>";
                                exit;
                            }
                            Auth::login($user, true);
                            return redirect($redirect_to);
                            exit;
                }

                else if( !isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strpos($_SERVER['REQUEST_URI'], "/oauthcallback") !== false || isset($_REQUEST['code']))) {
                    if(session_id() == '' || !isset($_SESSION))
                        session_start();

                    if (!isset($_REQUEST['code'])){
                        if(isset($_REQUEST['error_description'])){
                            exit($_REQUEST['error_description']);
                        }
                        else if(isset($_REQUEST['error']))
                        {
                            exit($_REQUEST['error']);
                        }
                        exit('Invalid response');
                    } else{

                        try {

                            $currentappname = "";

                            if (isset($_SESSION['appname']) && !empty($_SESSION['appname']))
                                $currentappname = $_SESSION['appname'];
                            else if (isset($_REQUEST['state']) && !empty($_REQUEST['state'])){
                                $currentappname = base64_decode($_REQUEST['state']);
                            }

                            if (empty($currentappname)) {
                                exit('No request found for this application.');
                            }

                            $currentapp = json_decode(DB::get_option('mo_oauth_provider_config'),true);
                            $username_attr = $currentapp['username_attr'];
                            
                            if (!$currentapp){
                                exit('Application not configured.');
                            }
                            $resourceownerdetailsurl = $currentapp['resourceownerdetailsurl'];
                            $mo_oauth_handler = new Mo_OAuth_Hanlder();
                            if(isset($currentapp['apptype']) && $currentapp['apptype']=='openidconnect') {
                                // OpenId connect

                                if( isset( $_REQUEST['id_token'] ) ) {
                                    $idToken = $_REQUEST['id_token'];
                                } else {
                                    if(!isset($currentapp['send_headers']))
                                        $currentapp['send_headers'] = false;
                                    if(!isset($currentapp['send_body']))
                                        $currentapp['send_body'] = false;
                                    $tokenResponse = $mo_oauth_handler->getIdToken($currentapp['accesstokenurl'], 'authorization_code',
                                            $currentapp['clientid'], $currentapp['clientsecret'], $_GET['code'], $currentapp['redirecturi'], $currentapp['send_headers'], $currentapp['send_body']);
            
                                    $idToken = isset($tokenResponse["id_token"]) ? $tokenResponse["id_token"] : $tokenResponse["access_token"];
                                    
                                }   
                
                                if(!$idToken){
                                    exit('Invalid token received.');
                                }
                                else{
                                    $resourceOwner = $mo_oauth_handler->getResourceOwnerFromIdToken($idToken);
                                }

                            } else {
                                // echo "OAuth";

                                $accessTokenUrl = $currentapp['accesstokenurl'];
                                
                                if(!isset($currentapp['send_headers']))
                                    $currentapp['send_headers'] = false;
                                if(!isset($currentapp['send_body']))
                                    $currentapp['send_body'] = false;

                                if(strpos($currentapp['authorizeurl'], 'clever.com/oauth') != false || 
                                    $currentapp['appId'] == 'bitrix24') {
                                    $accessToken = $mo_oauth_handler->getAccessTokenCurl($accessTokenUrl, 'authorization_code', $currentapp['clientid'], $currentapp['clientsecret'], $_GET['code'], $currentapp['redirecturi'], $currentapp['send_headers'], $currentapp['send_body']);
                                } else {
                                    $accessToken = $mo_oauth_handler->getAccessToken($accessTokenUrl, 'authorization_code', $currentapp['clientid'], $currentapp['clientsecret'], $_GET['code'], $currentapp['redirecturi'], $currentapp['send_headers'], $currentapp['send_body']);
                                }
                                if(!$accessToken){
                                    exit('Invalid token received.');
                                }

                                if (substr($resourceownerdetailsurl, -1) == "=") {
                                    $resourceownerdetailsurl .= $accessToken;
                                }
                                $resourceOwner = $mo_oauth_handler->getResourceOwner($resourceownerdetailsurl, $accessToken);
                            }
                            dd($resourceOwner);
                            if(!empty($username_attr))
                                $username = $this->mo_oauth_client_getnestedattribute($resourceOwner, $username_attr); //$resourceOwner[$email_attr];

                            if(empty($username) || "" === $username){
                                exit('Username not received. Check your <b>Attribute Mapping</b> configuration.');
                            }
                            
                            // $this->mo_oauth_login_user($username,$currentapp['redirecturi']);
                            $redirect_to = $currentapp['redirecturi'];
                            $username_attr = $username;
                                    $redirect_to = str_replace('ssologin.php','', $redirect_to);
                            $user = User::where('email', $username_attr)->first();

                            if ($user == null ) { // Create User if not existing
                                $user = new User();
                                $user->email = $username_attr;
                            }

                            $name_column_exists = $user->getConnection()->getSchemaBuilder()->hasColumn($user->getTable(),'name');
                            if($name_column_exists) {
                                $user->name = $username_attr;
                            }
                            $user->password = Hash::make(Str::random(8));

                            try {
                                $user->save();
                            } catch (\PDOException $e) {
                                if ($e->getCode() == '42S22')
                                    echo "<b>" . $e->getCode() . " : Database > Table > Column not found.</b> It seems your <b>Users table</b> does not have column(s) <b>" . implode(", ", array_keys($custom)) . "</b> as mapped in <a href=/setup.php>Custom Attribute Mapping</a>. Please check your <a href=/setup.php>Custom Attribute Mapping</a> and <b>Users table</b>";
                                exit;
                            }
                            Auth::login($user, true);
                            return redirect($redirect_to);
                            exit;
                        } catch (Exception $e) {

                            // Failed to get the access token or user details.
                            //print_r($e);
                            exit($e->getMessage());

                        }

                    }

                }
    }


    function mo_oauth_client_getnestedattribute($resource, $key){
        //echo $key." : ";print_r($resource); echo "<br>";
        if($key==="")
            return "";

        $keys = explode(".",$key);
        if(sizeof($keys)>1){
            $current_key = $keys[0];
            if(isset($resource[$current_key]))
                return $this->mo_oauth_client_getnestedattribute($resource[$current_key], str_replace($current_key.".","",$key));
        } else {
            $current_key = $keys[0];
            if(isset($resource[$current_key])) {
                return $resource[$current_key];
            }
        }
    }

    function mo_oauth_login_user($username_attr,$redirect_to){
        $redirect_to = str_replace('ssologin.php','', $redirect_to);
        $user = User::where('email', $username_attr)->first();

        if ($user == null ) { // Create User if not existing
            $user = new User();
            $user->email = $username_attr;
        }

        $name_column_exists = $user->getConnection()->getSchemaBuilder()->hasColumn($user->getTable(),'name');
        if($name_column_exists) {
            $user->name = $username_attr;
        }
        $user->password = Hash::make(Str::random(8));

        try {
            $user->save();
        } catch (\PDOException $e) {
            if ($e->getCode() == '42S22')
                echo "<b>" . $e->getCode() . " : Database > Table > Column not found.</b> It seems your <b>Users table</b> does not have column(s) <b>" . implode(", ", array_keys($custom)) . "</b> as mapped in <a href=/setup.php>Custom Attribute Mapping</a>. Please check your <a href=/setup.php>Custom Attribute Mapping</a> and <b>Users table</b>";
            exit;
        }
        Auth::login($user, true);
       return redirect($redirect_to);
    }
}
?>