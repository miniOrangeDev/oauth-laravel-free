<?php

use MiniOrange\Helper\DB;
use MiniOrange\Helper\CustomerDetails as CD;

if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['authorized']) && !empty($_SESSION['authorized'])) {
    if ($_SESSION['authorized'] != true) {
      
        header('Location: mo_oauth_admin_login.php');
        exit();
    }
}
else {
    header('Location: mo_oauth_admin_login.php');
    exit();
}
if(isset($_REQUEST['option']) and $_REQUEST['option'] == 'testattrmappingconfig'){
    var_dump($_REQUEST);exit;
    // exit;
    $mo_oauth_app_name = sanitize_text_field($_REQUEST['app']);
    // header("Location: https://www.google.com/");
    // wp_redirect(CD::oauth_get_current_domain().'?option=oauthredirect&app_name='. urlencode($mo_oauth_app_name)."&test=true");
 
}
if(isset($_POST['option']) and $_POST['option'] == 'test_config'){
    // exit;
    var_dump($_POST);exit;
    // $mo_oauth_app_name = $_POST['displayappname'];
    // header("Location: https://www.google.com/");
    // wp_redirect(CD::oauth_get_current_domain().'?option=oauthredirect&app_name='. urlencode($mo_oauth_app_name)."&test=true");
    // exit();
}

if(isset($_POST['option']) && $_POST['option'] == 'reset_config'){
    DB::update_option('client_id', NULL);
    DB::update_option('client_secret', NULL);
    DB::update_option('scope', NULL);
    DB::update_option('authorize_url', NULL);
    DB::update_option('access_token_url', NULL);
    DB::update_option('resource_owner_details_url', NULL);
    DB::update_option('domain', NULL);
    DB::update_option('realm', NULL);
    DB::update_option('tenant', NULL);
    DB::update_option('policy', NULL);
    DB::update_option('send_header', NULL);
    DB::update_option('send_body', NULL);
    DB::update_option('send_state', NULL);
    DB::update_option('login_attribute', NULL);
    DB::update_option('mo_oauth_message', "Settings reset successfully!");
}

if (isset($_POST['option']) && $_POST['option'] == 'save_connector_settings') {
    if($_POST['apptype'] == 'openidconnect'){
        if(isset($_POST['discovery']) && !empty($_POST['discovery']) && !is_null($_POST['discovery'])){
            $discovery_endpoint = $_POST['discovery'];
            $_POST['domain'] = stripslashes(rtrim($_POST['domain'],"/"));
            $discovery_endpoint = str_replace('realmname', $_POST['realm'], $discovery_endpoint);
            $discovery_endpoint = str_replace('tenant', $_POST['tenant'], $discovery_endpoint);
            $discovery_endpoint = str_replace('domain', $_POST['domain'], $discovery_endpoint);
            $discovery_endpoint = str_replace('policy', $_POST['policy'], $discovery_endpoint);

            $provider_se = null;
            if((filter_var($discovery_endpoint, FILTER_VALIDATE_URL))){      
                $arrContextOptions=array( 
                    "ssl"=>array(
                        "verify_peer"=>false,
                        "verify_peer_name"=>false,
                    ),
                );  
                $content=@file_get_contents($discovery_endpoint,false, stream_context_create($arrContextOptions));
                $provider_se = array();
                $scope = array();
                if($content){
                    $provider_se=json_decode($content);    
                    foreach ($provider_se->scopes_supported as $key => $value) {
                     $scope[$key] = $value;            
                    }  
                    $scope_list = array();
                    foreach ($scope as $key => $value) {
                        array_push($scope_list,  array('name' => $value,'value'=> $value ));
                    }
                    $_POST['mo_oauth_scopes_list'] = $scope_list;  
                    $scope = mo_oauth_get_scopes($scope);
                    $_POST['mo_oauth_scopes'] = $scope;

                    $_POST['authorizeurl'] = isset($provider_se->authorization_endpoint) ? stripslashes($provider_se->authorization_endpoint) : "";
                    $_POST['accesstokenurl'] = isset($provider_se->token_endpoint) ? stripslashes($provider_se->token_endpoint ) : "";
                    $_POST['resourceownerdetailsurl'] = isset($provider_se->userinfo_endpoint) ? stripslashes($provider_se->userinfo_endpoint) : "";
                }
            }
        }
    }
    if(isset($_POST['send_headers'])){
        DB::update_option('send_header', "true");
    } else{
        DB::update_option('send_header', "false");
    }
    if(isset($_POST['send_body'])){
        DB::update_option('send_body', "true");
    } else{
        DB::update_option('send_body', "false");
    }
    if(isset($_POST['send_state'])){
        DB::update_option('send_state', "true");
    } else{
        DB::update_option('send_state', "false");
    }
    DB::update_option('mo_oauth_provider_config',json_encode($_POST));
    DB::update_option('mo_oauth_message', 'Settings saved successfully!!');
    DB::update_option('oauth_provider_name', $_POST['displayappname']);
    DB::update_option('redirect_uri', CD::oauth_get_current_domain().'/ssologin.php');
    DB::update_option('client_id', $_POST['clientid']);
    DB::update_option('client_secret', $_POST['clientsecret']);
    DB::update_option('scope', $_POST['scope']);
    DB::update_option('authorize_url', $_POST['authorizeurl']);
    DB::update_option('access_token_url', $_POST['accesstokenurl']);
    DB::update_option('resource_owner_details_url', $_POST['resourceownerdetailsurl']);
    DB::update_option('domain', $_POST['domain']);
    DB::update_option('realm', $_POST['realm']);
    DB::update_option('tenant', $_POST['tenant']);
    DB::update_option('policy', $_POST['policy']);
    DB::update_option('login_attribute', $_POST['username_attr']);
}

    function mo_oauth_get_scopes($scopes){
        $pri_scopes = ['openid','email','profile'];
        $new_scopes = [];
        foreach( $pri_scopes as $key => $value ){
            if(in_array($pri_scopes[$key], $scopes) ){
                $new_scopes[$key] = $pri_scopes[$key];
            }
        }
        $new_scope_len = sizeof($new_scopes);
        if(3 > $new_scope_len){
            for ( $i = 2; $i >= $new_scope_len; $i--) {
                for ( $j = sizeof($scopes) - 1; $j >= 0; $j--) {
                    if(!in_array($scopes[$j], $new_scopes)){
                        $new_scopes[$i] = $scopes[$j];
                        $break;
                    }
                }
            }
        }
        return $new_scopes;

}
 

?>
    
