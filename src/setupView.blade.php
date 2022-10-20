<?php
use MiniOrange\Helper\CustomerDetails as CD; 
use MiniOrange\Helper\DB;

echo View::make('mooauth::menuView');
$applistjs = $applist;
$applist = json_decode($applist); 
$app = DB::get_option('mo_oauth_provider_config');
?><main class="app-content">
    <div class="app-title">
        <div>
            <h1>Plugin Settings</h1>
        </div>
    </div>
    <p id="oauth_message"></p>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <fieldset>
                    <div class="row">
                        <div class="col-lg-12">
                            <label id="provider_label" for="cars">Choose a OAuth Provider</label>
                            <br>
                              <select name="providers" id="providers"><?php 
                              foreach ($applist as $key => $value) {
                                 echo '<option value='.$key.'>'.$key.'</option>';
                              }
                                ?></select>
                            <br><br><hr>
                            <form method="POST" action="" id="oauth_form" >
                                <input type="hidden" name="appId" id="appId">
                                <input type="hidden" name="apptype" id="type">
                                <input type="hidden" name="option" value="save_connector_settings">
                                <input type="hidden" name="provider" value="provider">
                                <input type="hidden" name="discovery" value="">
                                @csrf
                                <table id="moOAuthTable" class="table">
                                    <tr><td>Display App Name<br><input type="text" name="displayappname" id="displayappname"></td></tr>
                                    <tr><td>Redirect / Callback URL<br><input id="redirect_uri" type="text" name="redirecturi" value="<?php echo CD::oauth_get_current_domain().'/ssologin.php'; ?>" readonly disabled><button type="button" onclick="moOauthLaravelCopy()"><i class="fa-regular fa-copy"></i></button></td></tr>
                                    <tr><td>Client ID<br><input id="client_id" type="text" name="clientid" value="<?php echo DB::get_option('client_id') ?>"></td></tr>
                                    <tr><td>Client Secret<br><input id="client_secret" type="password" name="clientsecret" value="<?php echo DB::get_option('client_secret') ?>"><button type="button" onclick="moOauthLaravelShow()"><i id="client_secret_icon" class="fa-solid fa-eye-slash"></i></button></td></tr>
                                    <tr><td>Scope<br><input type="text" name="scope" value="<?php echo DB::get_option('scope') ?>"></td></tr>
                                    <tr class="endpoints" style="display:none"><td>Authorization Endpoint<br><input type="text" name="authorizeurl" value="<?php echo DB::get_option('authorize_url') ?>"></td></tr>
                                    <tr class="endpoints" style="display:none"><td>Access Token Endpoint<br><input type="text" name="accesstokenurl" value="<?php echo DB::get_option('access_token_url') ?>"></td></tr>
                                    <tr class="endpoints" style="display:none"><td>Get Userinfo Endpoint<br><input type="text" name="resourceownerdetailsurl" value="<?php echo DB::get_option('resource_owner_details_url') ?>"></td></tr>
                                    <tr class="discovery Domain" style="display:none"><td>Domain<br><input type="text" name="domain" value="<?php echo DB::get_option('domain') ?>"></td></tr>
                                    <tr class="discovery Realm" style="display:none"><td>Realm<br><input type="text" name="realm" value="<?php echo DB::get_option('realm') ?>"></td></tr>
                                    <tr  class="discovery Tenant" style="display:none"><td>Tenant<br><input type="text" name="tenant" value="<?php echo DB::get_option('tenant') ?>"></td></tr>
                                    <tr class="discovery Policy" style="display:none"><td>Policy<br><input type="text" name="policy" value="<?php echo DB::get_option('policy') ?>"></td></tr>
                                    <tr><td>Send Client Credentials In<br><input type="checkbox" id="send_headers" name="send_headers" value="true">Header<input type="checkbox" id="send_body" name="send_body" value="true">Body</td></tr>
                                    <tr><td>Send State Parameter<br><input type="checkbox" id="send_state" name="send_state" value="true">State</td></tr>
                                    <tr><td>Login Attribute<br><input type="text" id="username_attr" name="username_attr" value="<?php echo DB::get_option('login_attribute') ?>"></td></tr>
                                </table>
                                <div class="buttons">
                                    <button type="submit" class="btn" id="save">Save Settings</button>
                                    <button type="button" class="btn" id="test_configuration" onclick="moOauthTestConfig()">Test Configuration</button>
                                    <button type="button" class="btn" id="reset" name="reset" onclick="moOauthResetConfig()">Reset</button>
                                </div>
                            </form>
                            <form method="POST" action="" id="reset_config_form">
                                <input type="hidden" name="option" value="reset_config">
                            </form>
                            <form method="POST" action="" id="test_config_form">
                                <input type="hidden" name="option" value="test_config">
                                <input type="hidden" id="app_name" value="">
                            </form>
                        </div>
                    </div> 
                </fieldset>
            </div>
        </div>
    </div>
</main>
<script type="text/javascript">
    function moOauthLaravelCopy() {
        var copyText = document.getElementById("redirect_uri");
        // Select the text field
        copyText.select();
        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);
    }
    function moOauthLaravelShow(){			//Toggle Client Secret when eye icon is clicked
        var field = document.getElementById("client_secret");
			var showButton = document.getElementById("client_secret_icon");
			if(field.type == "password"){
				field.type = "text";
				showButton.className = "fa-solid fa-eye";
			}
			else{  
					field.type = "password";
					showButton.className = "fa-solid fa-eye-slash";
			}
    }
    function moOauthResetConfig(){
        resetConfig();
        document.forms['reset_config_form']. submit();
        
    }
    function resetConfig(){
        document.getElementById('client_id').value = "";
    }
    function moOauthTestConfig(){
        var mo_oauth_app_name = jQuery("#displayappname").val();
        document.getElementById("app_name").value = mo_oauth_app_name;
        // console.log(document.getElementById("app_name"));
        var myWindow = window.open('<?php echo CD::oauth_get_current_domain(); ?>' + '/ssologin.php?option=testattrmappingconfig&app='+mo_oauth_app_name, "Test Attribute Configuration", "width=600, height=600");
    }
    // var disabled = document.getElementById("providers").disabled;
    // if(document.getElementById('save').clicked == true) {
    //     document.getElementById("providers").disabled = true;
    //     document.getElementById("reset").disabled = true;
    // } else if(document.getElementById('reset').clicked == true) {
    //     // if (disabled) {
    //         document.getElementById("providers").disabled = false;
    //         document.getElementById("save").disabled = false;
    //     // }
    // }
</script>
<script>
    jQuery('select[name="providers"]').change(function(){
        console.log("provider changed");
        jQuery('tr.discovery').hide();
        jQuery('tr.endpoints').hide();
        jQuery("input[name=scope]").val("");
        applist = '<?php echo json_encode($applist); ?>';
        applist = jQuery.parseJSON(applist);     
        for(var app in applist) {
            if(jQuery(this).val() == app) {            
                selected_app = applist[app];
                break;
            } 
        }
        console.log(selected_app);
        jQuery('input[name=displayappname]').val(app);
        jQuery('input[name=provider]').val(app);
        jQuery('input[name=apptype]').val(selected_app["type"]); 
        jQuery('input[name=discovery]').val(selected_app["discovery"]);

        var client_id = "<?php echo DB::get_option('client_id') ?>";
        if(client_id == "NULL" || client_id == ""){
            if(undefined != selected_app["input"]){
                for(i in selected_app["input"]){
                    console.log(i);
                    jQuery('tr.discovery.'+i).show();
                }
                if(undefined != selected_app["avl_domain"]){
                    jQuery("input[name=domain]").val(selected_app["avl_domain"]);
                }
            }
            else{
                jQuery('tr.endpoints').show();
                if(undefined != selected_app["authorize"])
                    jQuery("input[name=authorizeurl]").val(selected_app["authorize"]);
                if(undefined != selected_app["token"])      
                    jQuery("input[name=accesstokenurl]").val(selected_app["token"]); 
                if("openidconnect" != selected_app["type"] && undefined != selected_app["userinfo"])
                    jQuery("input[name=resourceownerdetailsurl]").val(selected_app["userinfo"]); 
            }
            if(undefined != selected_app["scope"] && "" != selected_app["scope"]){
                jQuery("input[name=scope]").val(selected_app["scope"]);
            }
            document.getElementById("providers").disabled = false;
        }
        else{
            if(undefined != selected_app["input"]){
                for(i in selected_app["input"]){
                    console.log(i);
                    jQuery('tr.discovery.'+i).show();
                }
                if(undefined != selected_app["avl_domain"]){
                    jQuery("input[name=domain]").val("<?php echo DB::get_option('domain') ?>");
                }
            }
            else{
                jQuery('tr.endpoints').show();
                if(undefined != selected_app["authorize"])
                    jQuery("input[name=authorizeurl]").val("<?php echo DB::get_option('authorize_url') ?>");
                if(undefined != selected_app["token"])      
                    jQuery("input[name=accesstokenurl]").val("<?php echo DB::get_option('access_token_url') ?>"); 
                if("openidconnect" != selected_app["type"] && undefined != selected_app["userinfo"])
                    jQuery("input[name=resourceownerdetailsurl]").val("<?php echo DB::get_option('resource_owner_details_url') ?>"); 
            }
            if(undefined != selected_app["scope"] && "" != selected_app["scope"]){
                jQuery("input[name=scope]").val("<?php echo DB::get_option('scope') ?>");
            }
            document.getElementById("providers").disabled = true;
        }            
    });

    app = '<?php echo $app; ?>'
    if('' !== app){
        try{
            app = jQuery.parseJSON(app)
            // console.log(app)
            // for (i in app){
            //     if(i != "option"){

            //         jQuery('input[name='+i+']').val(app[i]);
            //     }
            // }
            if(undefined !== app['send_headers'] && 'true' === app['send_headers'])
                jQuery('#send_headers').prop('checked', true);
            if(undefined !== app['send_state'] && 'true' === app['send_state'])
                jQuery('#send_state').prop('checked', true);
            if(undefined !== app['send_body'] && 'true' === app['send_body'])
                jQuery('#send_body').prop('checked', true);
            if(undefined !== app['provider']){
                jQuery("#providers").val(app['provider']).trigger("change");
            }
         }catch(error){
            console.log(error);
         }
    }
</script>
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
    exit();
}
?>