<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03-06-2019
 * Time: 12:56
 */

namespace MiniOrange\Helper;

use Illuminate\Support\Facades\DB as LaraDB;

class CustomerDetails
{
    public static function get_option($key)
    {
        try {
            $result = LaraDB::select('select * from mo_oauth_customer_details where mo_oauth_domain_name = ?', [self::oauth_get_current_domain()]);
            if( count($result) == 0 ) {
                header('Location: mo_oauth_create_tables');
                exit;
            } else {
                $result = $result[0];
            }
        } catch (\PDOException $e) {
            if ($e->getCode() == '42S02') {
                header('Location: mo_oauth_create_tables');
                exit;
            }
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            echo " $code \r\n $msg \r\n $trace \r\n";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
        return $result->$key;
    }

    public static function update_option($key, $value)
    {
        try {
            $result = LaraDB::table('mo_oauth_customer_details')->updateOrInsert([
                'mo_oauth_domain_name' => self::oauth_get_current_domain()
            ], [
                $key => $value
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            $trace = serialize($trace);

            echo " $code \r\n $msg \r\n $trace";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
    }

    public static function delete_option($key)
    {
        try {
            $result = LaraDB::table('mo_oauth_customer_details')->updateOrInsert([
                'mo_oauth_domain_name' => self::oauth_get_current_domain()
            ], [
                $key => ''
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            $trace = serialize($trace);
            echo " $code \r\n $msg \r\n $trace";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
    }

    protected static function get_options()
    {
        try {
            $result = LaraDB::select('select * from mo_oauth_customer_details where mo_oauth_domain_name = ?', [self::oauth_get_current_domain()]);
            if( count($result) == 0 ) {
                header('Location: mo_oauth_create_tables');
                exit;
            } else {
                $result = $result[0];
            }
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            echo " $code \r\n $msg \r\n $trace";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
    }

    public static function oauth_get_current_domain()
    {
        $http_host = $_SERVER['HTTP_HOST'];
        if (substr($http_host, -1) == '/') {
            $http_host = substr($http_host, 0, -1);
        }
        $request_uri = $_SERVER['REQUEST_URI'];
        if (substr($request_uri, 0, 1) == '/') {
            $request_uri = substr($request_uri, 1);
        }

        $is_https = (isset($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') == 0);
        $relay_state = 'http' . ($is_https ? 's' : '') . '://' . $http_host;
        return $relay_state;
    }
}