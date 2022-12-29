<?php
namespace MiniOrange\Helper;

use Illuminate\Database\Capsule\Manager as ConDB;
use Illuminate\Support\Facades\DB as LaraDB;
use MiniOrange\Classes\Actions\MoOauthDatabaseController as DC;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Console\Kernel as Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use PDOException;
use phpDocumentor\Reflection\Types\Null_;
use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper as TDB;
 
class OauthDB extends Controller
{
    // private static $domain_name;
    // // define('DOMAIN_NAME', $domain_name);
    // function __construct() {
    //     self::oauth_get_current_domain() = self::oauth_get_current_domain();
    // }

    public static function get_option($key)
    {
        try {
            $result = LaraDB::select('select * from mo_oauth_config where mo_oauth_domain_name = ?', [self::oauth_get_current_domain()]);
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
            $result = LaraDB::table('mo_oauth_config')->updateOrInsert([
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
            $result = LaraDB::table('mo_oauth_config')->updateOrInsert([
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

    public static function get_registered_user()
    {
        try {
            $result = LaraDB::select('select * from mo_oauth_admin where mo_oauth_domain_name = ?', [self::oauth_get_current_domain()]);
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
        }
        if (empty($result->email))
            return null;
        else
            return $result;
    }

    public static function register_user($email, $password)
    {
        try {
            LaraDB::table('mo_oauth_admin')->updateOrInsert([
                'mo_oauth_domain_name' => self::oauth_get_current_domain()
            ], [
                'email' => $email,
                'password' => $password
            ]);
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

    public static function get_column($table, $key)
    {
        self::startConnection();
        $option = ConDB::table($table)->where(['mo_oauth_domain_name' => self::oauth_get_current_domain()])->first()->$key;
        return $option;
    }

    protected static function startConnection()
    {
        $path = realpath(__DIR__.'/../../../../laravel/framework/src/Illuminate/Support/helpers.php');
        include_once $path;

        $connection = array(
            'driver' => getenv('DB_CONNECTION'),
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'collation' => 'default'
        );

        $Capsule = new ConDB();
        $Capsule->addConnection($connection);
        $Capsule->setAsGlobal(); // this is important. makes the database manager object globally available
        $Capsule->bootEloquent();
        try {
            if (ConDB::table('mo_oauth_config')->get() == NULL) {
                ConDB::table('mo_oauth_config')->updateOrInsert([
                    'mo_oauth_domain_name' => self::oauth_get_current_domain()
                ], [
                    'mo_oauth_host_name' => 'https://login.xecurify.com'
                ]);
            }
        } catch (PDOException $e) {

            if ($e->getCode() === '42S02') {

                header('Location: mo_oauth_create_tables');
                exit();
            }
            if ($e->getCode() == 2002) {
                echo 'It looks like your <b>Database is offline</b>. Please make sure that your database is up and running, and try again.<a style="text-decoration:none" href="/"><u>Click here to go back to your website</u></a>';
                exit();
            }
        }
    }

    protected static function get_options()
    {
        try {
            $result = LaraDB::select('select * from mo_oauth_config where mo_oauth_domain_name = ?', [self::oauth_get_current_domain()]);
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

    private static function oauth_get_current_domain()
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

?>
