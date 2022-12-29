<?php

namespace MiniOrange\Classes\Actions;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MoOauthDatabaseController extends Controller
{

    public function createTables()
    {
        $migration_path = explode('vendor', __DIR__ . '/../../migrations', 2)[1];
        echo "Setting up database for MiniOrange oauth SP for Laravel...<br>";
        try {
            Artisan::call('migrate:refresh', array(
                '--path' => 'vendor' . $migration_path,
                '--force' => TRUE
            ));
        } catch (\PDOException $e) {
            echo $e->errorInfo[2];
            exit;
            echo "Could not create tables. Please check your Database Configuration and Connection and try again.";
            exit();
        }
        header('Location: mo_oauth_admin');
        exit();
    }
}