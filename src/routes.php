<?php

Route::get('mo_oauth_admin', 'MiniOrange\Classes\Actions\MoAdminController@launch');

Route::get('mo_oauth_register.php', 'MiniOrange\Classes\Actions\MoRegisterController@launch');
Route::post('mo_oauth_register.php', 'MiniOrange\Classes\Actions\MoRegisterController@launch');

Route::get('mo_oauth_account.php', 'MiniOrange\Classes\Actions\MoAccountController@launch');
Route::post('mo_oauth_account.php', 'MiniOrange\Classes\Actions\MoAccountController@launch');

Route::get('mo_oauth_admin_login.php', 'MiniOrange\Classes\Actions\MoAdminLoginController@launch');
Route::post('mo_oauth_admin_login.php', 'MiniOrange\Classes\Actions\MoAdminLoginController@launch');

Route::get('mo_oauth_setup.php', 'MiniOrange\Classes\Actions\MoSetupController@launch');
Route::post('mo_oauth_setup.php', 'MiniOrange\Classes\Actions\MoSetupController@launch');

Route::get('ssologin.php', 'MiniOrange\Classes\Actions\MoSSOController@launch');
Route::post('ssologin.php', 'MiniOrange\Classes\Actions\MoSSOController@launch');

Route::get('mo_oauth_admin_logout.php', 'MiniOrange\Classes\Actions\MoAdminLogoutController@launch');

Route::get('mo_oauth_how_to_setup.php', 'MiniOrange\Classes\Actions\MoHowToSetupController@launch');
Route::post('mo_oauth_how_to_setup.php', 'MiniOrange\Classes\Actions\MoHowToSetupController@launch');

Route::get('mo_oauth_support.php', 'MiniOrange\Classes\Actions\MoSupportController@launch');
Route::post('mo_oauth_support.php', 'MiniOrange\Classes\Actions\MoSupportController@launch');

Route::get('mo_oauth_create_tables', 'MiniOrange\Classes\Actions\DatabaseController@createTables');