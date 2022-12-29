<?php

namespace MiniOrange\Classes\Actions;

use Illuminate\Routing\Controller;

class MoOauthSetupController extends Controller {
    public function launch() {
        $appList = file_get_contents(dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'defaultapps.json');
        $data = array('applist',$appList);
        include_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'mo_oauth_setup.php';
        include_once dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'jsLoader.php';
        @include('mooauth::menuView');
        return view('mooauth::setupView')->with('applist',$appList);
    }
}