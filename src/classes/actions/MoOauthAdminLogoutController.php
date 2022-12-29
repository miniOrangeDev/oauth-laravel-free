<?php

namespace MiniOrange\Classes\Actions;

use Illuminate\Routing\Controller;

class MoOauthAdminLogoutController extends Controller {
    public function launch() {
        include_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'mo_oauth_admin_logout.php';
    }
}