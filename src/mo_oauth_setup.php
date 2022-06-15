<?php

use MiniOrange\Helper\DB;

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
                    $_POST['mo_oauth_scopes_list'] = json_encode($scope_list);  
                    $scope = mo_oauth_get_scopes($scope);
                    $_POST['mo_oauth_scopes'] = json_encode($scope);

                    $_POST['authorizeurl'] = isset($provider_se->authorization_endpoint) ? stripslashes($provider_se->authorization_endpoint) : "";
                    $_POST['accesstokenurl'] = isset($provider_se->token_endpoint) ? stripslashes($provider_se->token_endpoint ) : "";
                    $_POST['resourceownerdetailsurl'] = isset($provider_se->userinfo_endpoint) ? stripslashes($provider_se->userinfo_endpoint) : "";
                }
            }
        }
    }
    DB::update_option('mo_oauth_provider_config',json_encode($_POST));
    DB::update_option('mo_oauth_message', 'Settings saved successfully!!');
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
    
