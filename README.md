# oauth-laravel-free
Laravel OAuth Single Sign On Solution. Make your Laravel application as OAuth Client using this plugin. Laravel end users can login into Laravel application using their OAuth Provider / Server credentials.

# Steps to setup connector
1. Run command to install this connector into your Laravel application - composer require miniorange/oauth-laravel-free
2. Access <laravel-application-domain>/mo_oauth_admin
3. Register yourself
4. Login using the credentials that you used during registration
5. Setup plugin configuration
6. Once done use SSO url to initiate login - <laravel-application-domain>/ssologin.php?option=oauthredirect
