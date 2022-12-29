<?php

use Illuminate\Database\Migrations\Migration;

class MoLaravelOAuthPreMigrateFileDelUp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        if(isset($_SERVER['HTTP_HOST'])){
            $src = dirname(__DIR__) .'/includes/js/main.js';
            $dst = public_path() . "/miniorange/sso_oauth_free/includes/js/main.js";
            copy($src, $dst);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
