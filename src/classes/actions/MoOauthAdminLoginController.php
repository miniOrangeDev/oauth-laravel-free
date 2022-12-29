<?php

namespace MiniOrange\Classes\Actions;

use Illuminate\Routing\Controller;

class MoOauthAdminLoginController extends Controller {
    public function launch() {
        include_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'mo_oauth_admin_login.php';
        include_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'jsLoader.php';
        return view('mooauth::adminLoginView');
    }
}