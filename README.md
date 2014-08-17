## Facebook PHP SDK v4 for Laravel 4

[![Build Status](https://travis-ci.org/pingpong-labs/facebook.svg?branch=master)](https://travis-ci.org/pingpong-labs/facebook)

### Server Requirement

This package is require PHP 5.4 or higher.

### Installation

Open your composer.json file, and add the new required package.
```
   "pingpong/facebook": "1.0.*"
```
Next, open a terminal and run.
```
composer update
```

Next, Add new service provider in `app/config/app.php`.

```php
  'Pingpong\Facebook\FacebookServiceProvider',
```

Next, Add new aliases in `app/config/app.php`.

```php
   'Facebook' => 'Pingpong\Facebook\Facades\Facebook',
```

Next, publish the configuration.
```
php artisan config:publish pingpong/facebook
```

Done.

### Usage

First, you must set the app_id and the app_secret in the configuration file. You can also set the default scope and the redirect url.

```php
return array(
	'app_id'		=>	'',
	'app_secret'	=>	'',
	'redirect_url'	=>	url('facebook/callback'),
	'scope'			=>  array(
		'publish_actions',
		'email'
	)
);
```

Get facebook login url.
```php
Facebook::getLoginUrl();
```

You can also update the scope.
```php
$scope = array('email');

Facebook::getLoginUrl($scope);
```

Authenticate the user and get the permissions. This will automatically redirect the user to the facebook login url.
```php
Facebook::authenticate();
```

If you want to update/override the scope, you can add the scope in the first parameter.
```php
$scope = array('email');

Facebook::authenticate($scope);
```

You can also set the version of Facebook API want to use.
```php
$version = 'the-version';
$scope = array('email');

Facebook::authenticate($scope, $version);
```

Get user profile for the current logged in user.
```php
Facebook::getProfile();
```

Logout the current active user.
```php
Facebook::destroy();
// or
Facebook::logout();
```

Call the Facebook API.
```php
Facebook::api($method, $path, $parameters, $version);

Facebook::api('GET', '/me');

Facebook::api('POST', '/me/feed', $parameters);

Facebook::api('PUT', '/path', $parameters);

Facebook::api('PATCH', '/path', $parameters);

Facebook::api('DELETE', '/path/to', $parameters);
```

Helper method for call Facebook API.

GET Request
```php
Facebook::get('/path', $parameters);
```

POST Request
```php
Facebook::post('/path', $parameters);
```

PUT Request
```php
Facebook::put('/path', $parameters);
```

PATCH Request
```php
Facebook::patch('/me', $parameters);
```

DELETE Request
```php
Facebook::delete('/me', $parameters);
```

### License

This package is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).