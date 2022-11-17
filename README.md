# What is Single Sign-On (SSO)?
**Single Sign-On** is an authorization and authentication process that enables an user to connect to multiple enterprise applications using a single set of credentials. Simply put, SSO combines multiple application login pages into just one, allowing you to submit credentials just once and gain access to all the applications without having to log in to each one individually. End users save time and effort by not having to sign into and out of a variety of on-premises, web and cloud applications on a regular basis.

SSO is a critical component of the Identity and Access Management or access privileges services. SSO solution perfectly implemented within an enterprise simplifies overall password management, improving productivity and security, lowering the likelihood of weak, lost, or forgotten passwords

# Laravel OAuth SSO 
Laravel OAuth Single Sign On Solution makes your Laravel application as OAuth Client using this package. Laravel end users can login into your Laravel application using their OAuth Provider / Server credentials.

We support all known OAuth Providers - Azure AD, Azure B2C, Office 365, AWS Cognito, Classlink, Discord, Clever, Ping, Keycloak, WHMCS, Okta, WSO2, Identity Server, Onelogin, Salesforce, G Suite / Google Apps, Invision Community, Slack, Amazon, Twitter, Apple, ID.me, Shell, Cisco Webex, Auth0, miniOrange etc. OAuth Single Sign on (SSO) acts as a OAuth 2.0 Client and securely authenticate users with your OAuth 2.0 Provider.

## Requirements
* Laravel - 7.0+
* PHP - ^5.1 || ^7.1 || ^8.0

## Installation - Composer
1. Install the package via composer in your Laravel app's main directory.
````
composer require miniorange/oauth-laravel-free
````

2. After successful installation of package, go to your Laravel app in the browser and enter

    ***{laravel-application-domain}/mo_oauth_admin***

3. The package will start setting up your database for you and then redirect you to the admin registration page where you can register or login with miniOrange and setup your OAuth Provider.

    ![This is plugin login page](https://plugins.miniorange.com/wp-content/uploads/2022/11/miniorange-login-dashboard.webp)
    
## Configuring the package

1. After login, you will see the OAuth provider Settings option, where you will get the Redirect/Callback URL. Keep it handy as it will be required later to configure your OAuth Provider.

    ![This is plugin setting page](https://cdn.discordapp.com/attachments/983596344147062894/1036872527978442824/laravel-oauth-redirect-url.png)
    
2. Choose an OAuth Provider from the dropdown. For e.g. WordPress

    ![This is plugin setting page](https://plugins.miniorange.com/wp-content/uploads/2022/11/select-wordpress-oauth-provider.webp)

3. Use your OAuth Provider details like **Client ID** and **Client Secret** to   configure the plugin. After that, you can enter the **Scope**, **Authorization Endpoint**, **Access Token Endpoint**, **GetUserinfo Endpoint**, **Realm**, **Domain**, **Tenant** (as per your OAuth Provider or use the default ones provided already).
You can send the client credentials in header or body and also send state parameter accordingly.
    
    ![This is plugin setting page](https://plugins.miniorange.com/wp-content/uploads/2022/11/laravel-plugin-client-credentails.webp)
    
    ![This is plugin setting page](https://plugins.miniorange.com/wp-content/uploads/2022/11/laravel-authorization-enpoint-save-setting.webp)
    
4. Click on Save Settings button.
    
## Test Configuration
1. You can test if the package is configured properly or not by clicking on the Test Configuration button. You should see a Test Successful screen as shown below along with the user's attribute values.

    ![This is test configuration page](https://plugins.miniorange.com/wp-content/uploads/2022/11/laravel-authorization-enpoint-save-setting.webp)

    ![This is test configuration page](https://plugins.miniorange.com/wp-content/uploads/2022/11/laravel-wordpress-test-configuration.webp)
    
## Adding Single Sign On button on the application login page (Optional)

Once the package is installed, you can add a **Single Sign On** button in your application login page using these commands in order:

1. Install the Laravel UI Package.
````
composer require laravel/ui
````
2. Generate Auth Routes using VueJs
````
php artisan ui vue --auth
````
3. Install Node modules and run the development
````
npm install && npm run dev
````
4. Migrate and update the database
````
php artisan migrate 
````

The Laravel application login page should look something like this then.

![This is plugin login page](https://plugins.miniorange.com/wp-content/uploads/2020/11/laravel-sso-button.webp)


# Features
The features provided in the free and premium are listed here.

| Free Plan                                      | Premium Plan                                              |
| :--------------------------------------------: |:---------------------------------------------------------:|
| Simple and easy-to-use admin UI                | Simple and easy-to-use admin UI                           |
| Unlimited SSO Authentications                  |Unlimited SSO Authentications                              |
| Just In Time User Provisioning / Auto-creation | Just In Time User Provisioning / Auto-creation            |
| Account Linking                                | Account Linking                                           |
| Basic Attribute Mapping                        | Advanced Attribute Mapping                                |
|                                                |Custom Attribute Mapping                                   |
|                                                | OAuth/OpenID Supported Grant Types                        |
|                                                | Redirect all SSO users to specific URL after Login/Logout |
|                                                | JWT Vadilation Support                                    |
|                                                | Protect Complete Site with SSO                            |
|                                                | Domain Restrictions                                       |

# Feature Description

* **Advanced and Custom Attribute Mapping**

    It allows you to map the recieved custom attributes sent by your OAuth Provider to the OAuth Client _(Laravel Application)_.
* **Auto-create users in Laravel**

    Creates the users from the OAuth Provider to OAuth Client (Laravel Application) when SSO is done.

* **OAuth/OpenID Supported Grant Types**

    Multiple grant type support like Authorization Code Grant, Password Grant, Client Credentials Grant, Implicit Grant, Refresh Token Grant and Authorization Code Grant with PKCE.
    
* **JWT Vadilation Support**

    HSA and RSA alogirthm support for JWT validation.
    

* **Protect Complete Site and Auto-Redirect**

    Asking user to login via SSO if the user session does not exist everytime the site is accessed.


# Single Sign On (SSO)

The Single Sign On can be initiated using ***{laravel-application-domain}/ssologin.php?option=oauthredirect*** or the Single Sign On button (if added using the commands above) on the login page of the Laravel application.