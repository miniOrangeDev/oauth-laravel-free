<?php

namespace MiniOrange;

use MiniOrange\Classes\Actions\SendAuthnRequest;
use MiniOrange\Helper\Utilities;

final class OauthLogin
{

    public function __construct()
    {
        try {
            SendAuthnRequest::execute();
        } catch (\Exception $e) {
            Utilities::showErrorMessage($e->getMessage());
        }
    }
}

new OauthLogin();