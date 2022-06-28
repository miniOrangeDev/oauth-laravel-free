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
            <h1>
                <i class="fa fa-gear"></i> Plugin Settings
            </h1>
        </div>
        <ul class="app-breadcrumb breadcrumb">
            <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
            <li class="breadcrumb-item"><a href="#">Plugin Settings</a></li>
        </ul>
    </div>
    <p id="oauth_message"></p>
    <div class="row">
        <div class="col-md-12">
            <div class="tile">
                <fieldset>
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="cars">Choose a OAuth Provider:</label>
                              <select name="providers" id="providers"><?php 
                              foreach ($applist as $key => $value) {
                                 echo '<option value='.$key.'>'.$key.'</option>';
                              }
                                ?></select>
                            <form method="POST" action="" id="oauth_form" >
                                <input type="hidden" name="appId" id="appId">
                                <input type="hidden" name="apptype" id="type">
                                <input type="hidden" name="option" value="save_connector_settings">
                                <input type="hidden" name="provider" value="provider">
                                <input type="hidden" name="discovery" value="">
                                @csrf
                                <table id="moOAuthTable" class="table">
                                    <tr><td>Display App Name: </td><td><input type="text" name="displayappname"></td></tr>
                                    <tr><td>Redirect / Callback URL: </td><td><input type="text" name="redirecturi" value="<?php echo CD::oauth_get_current_domain().'/ssologin.php'; ?>" readonly></td></tr>
                                    <tr><td>Client ID: </td><td><input type="text" name="clientid"></td></tr>
                                    <tr><td>Client Secret: </td><td><input type="password" name="clientsecret"></td></tr>
                                    <tr><td>Scope: </td><td><input type="text" name="scope"></td></tr>
                                    <tr class="endpoints" style="display:none"><td>Authorization Endpoint: </td><td><input type="text" name="authorizeurl"></td></tr>
                                    <tr class="endpoints" style="display:none"><td>Access Token Endpoint: </td><td><input type="text" name="accesstokenurl"></td></tr>
                                    <tr class="endpoints" style="display:none"><td>Get Userinfo Endpoint: </td><td><input type="text" name="resourceownerdetailsurl"></td></tr>
                                    <tr class="discovery Domain" style="display:none"><td>Domain: </td><td><input type="text" name="domain"></td></tr>
                                    <tr class="discovery Realm" style="display:none"><td>Realm: </td><td><input type="text" name="realm"></td></tr>
                                    <tr  class="discovery Tenant" style="display:none"><td>Tenant: </td><td><input type="text" name="tenant"></td></tr>
                                    <tr class="discovery Policy" style="display:none"><td>Policy: </td><td><input type="text" name="policy"></td></tr>
                                    <tr><td>Send client credentials in: </td><td><input type="checkbox" id="send_headers" name="send_headers" value="true">Header<input type="checkbox" id="send_body" name="send_body" value="true">Body</td></tr>
                                    <tr><td>Send State Parameter: </td><td><input type="checkbox" id="send_state" name="send_state" value="true">State</td></tr>
                                    <tr><td>Login Attribute: </td><td><input type="text" id="username_attr" name="username_attr" value=""></td></tr>
                                    <tr><td></td><td><button class="btn" id="save">Save Settings</button></td></tr>
                                </table>
                            </form>
                        </div>
                    </div> 
                </fieldset>
            </div>
        </div>
    </div>
</main>
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
});
    app = '<?php echo $app; ?>'
    if('' !== app){
        try{
            app = jQuery.parseJSON(app)
            console.log(app)
            for (i in app){
               jQuery('input[name='+i+']').val(app[i]);       
            }
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