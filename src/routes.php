<?php

Route::get('mo_oauth_admin', 'MiniOrange\Classes\Actions\MoOauthAdminController@launch');

Route::get('mo_oauth_register.php', 'MiniOrange\Classes\Actions\MoOauthRegisterController@launch');
Route::post('mo_oauth_register.php', 'MiniOrange\Classes\Actions\MoOauthRegisterController@launch');

Route::get('mo_oauth_account.php', 'MiniOrange\Classes\Actions\MoOauthAccountController@launch');
Route::post('mo_oauth_account.php', 'MiniOrange\Classes\Actions\MoOauthAccountController@launch');

Route::get('mo_oauth_admin_login.php', 'MiniOrange\Classes\Actions\MoOauthAdminLoginController@launch');
Route::post('mo_oauth_admin_login.php', 'MiniOrange\Classes\Actions\MoOauthAdminLoginController@launch');

Route::get('mo_oauth_setup.php', 'MiniOrange\Classes\Actions\MoOauthSetupController@launch');
Route::post('mo_oauth_setup.php', 'MiniOrange\Classes\Actions\MoOauthSetupController@launch');

Route::get('ssologin.php', 'MiniOrange\Classes\Actions\MoOauthSSOController@launch');
Route::post('ssologin.php', 'MiniOrange\Classes\Actions\MoOauthSSOController@launch');

Route::get('mo_oauth_admin_logout.php', 'MiniOrange\Classes\Actions\MoOauthAdminLogoutController@launch');

Route::get('mo_oauth_how_to_setup.php', 'MiniOrange\Classes\Actions\MoOauthHowToSetupController@launch');
Route::post('mo_oauth_how_to_setup.php', 'MiniOrange\Classes\Actions\MoOauthHowToSetupController@launch');

Route::get('mo_oauth_support.php', 'MiniOrange\Classes\Actions\MoOauthSupportController@launch');
Route::post('mo_oauth_support.php', 'MiniOrange\Classes\Actions\MoOauthSupportController@launch');

Route::get('mo_oauth_trial.php', 'MiniOrange\Classes\Actions\MoOauthTrialsController@launch');
Route::post('mo_oauth_trial.php', 'MiniOrange\Classes\Actions\MoOauthTrialsController@launch');

Route::get('mo_oauth_create_tables', 'MiniOrange\Classes\Actions\MoOauthDatabaseController@createTables');