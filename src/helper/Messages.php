<?php

namespace MiniOrange\Helper;

/**
 * This class lists down all of our messages to be shown to the admin or
 * in the frontend. This is a constant file listing down all of our
 * constants. Has a parse function to parse and replace any dynamic
 * values needed to be inputed in the string. Key is usually of the form
 * {{key}}
 */
class OauthMessages
{
    //General Flow Messages
    const ERROR_OCCURRED = 'An error occured while processing your request. Please try again.';

    //Licensing Messages
    const INVALID_LICENSE = 'Invalid domain or credentials.';

    //cURL Error
    const CURL_ERROR = 'ERROR: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP cURL extension</a> 
                                            is not installed or disabled. Query submit failed.';

    const SETTINGS_SAVED = 'Settings saved successfully.';

    //oauth SSO Error Messages


    /**
     * Parse the message
     * @param $message
     * @param array $data
     * @return mixed
     */
    public static function parse($message, $data = array())
    {
        $message = constant("self::" . $message);
        foreach ($data as $key => $value) {
            $message = str_replace("{{" . $key . "}}", $value, $message);
        }
        return $message;
    }
}