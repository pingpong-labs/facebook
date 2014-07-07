<?php namespace Pingpong\Facebook;

if( ! session_id())
{
	session_start();
}

use Facebook\GraphUser;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;

class Facebook
{
	/**
	 * The laravel application instance.
	 * 
	 * @var \Illuminate\Foundation\Application
	 */
	protected $app;

	/**
	 * String Facebook AppId
	 * 
	 * @var string
	 */
	protected $appId;

	/**
	 * @var \Facebook\FacebookRedirectLoginHelper
	 */
	protected $facebookHelper;

	/**
	 * @var \Facebook\FacebookSession
	 */
	protected $facebookSession;

	/**
	 * The constructor.
	 * 
	 * @param \Illuminate\Foundation\Application $app       
	 * @param string|null $appId     
	 * @param string|null $appSecret 
	 */
	public function __construct($app, $appId = null, $appSecret = null)
	{
		$appId 			= $appId ?: $app['config']->get('facebook::app_id');
		$appSecret 		= $appSecret ?: $app['config']->get('facebook::app_secret');
		$redirectUrl 	= $app['config']->get('facebook::redirect_url');

		$this->app 		= $app;
		$this->appId 	= $appId;

		$this->facebookSession = FacebookSession::setDefaultApplication($appId, $appSecret);
		$this->facebookHelper  = new FacebookRedirectLoginHelper($redirectUrl, $appId, $appSecret);
	}

	/**
	 * Get AppId.
	 * 
	 * @return string 
	 */
	public function getAppId()
	{
		return $this->appId;
	}

	protected function getScope($merge = array())
	{
		if(count($merge) > 0) return $merge;

		return $this->app['config']->get('facebook::scope');
	}
	/**
	 * Get Login Url.
	 * 
	 * @return string 
	 */
	public function getLoginUrl($scope = array(), $version = null)
	{
		$scope = $this->getScope($scope);

		return $this->facebookHelper->getLoginUrl($scope, $version);
	}

	/**
	 * Redirect to the facebook login url.
	 * 
	 * @return Response 
	 */
	public function authenticate($scope = array(), $version = null)
	{
		return $this->app['redirect']->to($this->getLoginUrl($scope, $version));
	}

	/**
	 * Get the facebook session (access token) when redirected back.
	 * 
	 * @return mixed 
	 */
	public function getSessionFromRedirect()
	{
		$session = $this->facebookHelper->getSessionFromRedirect();
	  	
	  	$this->app['session']->put('facebook.session', $session);
	  	
	  	return $session;
	}

	/**
	 * Get token when redirected back from facebook.
	 * 
	 * @return string 
	 */
	public function getTokenFromRedirect()
	{
		$session = $this->getSessionFromRedirect();

		return $session ? $session->getToken() : null;
	}

	/**
	 * Determine whether the "facebook.access_token".
	 * 
	 * @return boolean
	 */
	public function hasSessionToken()
	{
		return $this->app['session']->has('facebook.access_token');	
	}

	/**
	 * Get the facebook access token via Session laravel.
	 * 
	 * @return string 
	 */
	public function getSessionToken()
	{
		return $this->app['session']->get('facebook.access_token');
	}

	/**
	 * Put the access token to the laravel session manager.
	 * 
	 * @param  string $token 
	 * @return void        
	 */
	public function putSessionToken($token)
	{
		$this->app['session']->put('facebook.access_token', $token);	
	}

	/**
	 * Get the access token. If the current access token from session manager exists,
	 * then we will use them, otherwise we get from redirected facebook login.
	 * 
	 * @return mixed 
	 */
	public function getAccessToken()
	{
		if($this->hasSessionToken()) return $this->getSessionToken();

		return $this->getTokenFromRedirect();
	}

	/**
	 * Get callback from facebook.
	 * 
	 * @return boolean 
	 */
	public function getCallback()
	{
		$token = $this->getAccessToken();
		if( ! empty($token))
		{
			$this->putSessionToken($token);
			return true;
		}
		return false;
	}

	/**
	 * Get facebook session from laravel session manager.
	 * 
	 * @return string|mixed 
	 */
	public function getFacebookSession()
	{
		return $this->app['session']->get('facebook.session');
	}

	/**
	 * Destroy all facebook session.
	 * 
	 * @return void 
	 */
	public function destroy()
	{
		$this->app['session']->forget('facebook.session');
		$this->app['session']->forget('facebook.access_token');
	}

	/**
	 * Logout the current user.
	 * 
	 * @return void
	 */
	public function logout()
	{
	 	$this->destroy();
	}

	/**
	 * Facebook API Call.
	 * 
	 * @param  string $method     The request method.
	 * @param  string $path       The end points path.
	 * @param  mixed  $parameters Parameters.
	 * @param  string $version    The specified version of Api.
	 * @param  mixed  $etag
	 * @return mixed
	 */
	public function api($method, $path, $parameters  = null, $version = null, $etag = null)
	{
		$session = $this->getFacebookSession();

		$request = with(new FacebookRequest($session, $method, $path, $parameters, $version, $etag))
			->execute()
			->getGraphObject(GraphUser::className())
		;

		return $request;
	}

	/**
	 * Facebook API Request with "GET" method.
	 * 
	 * @param  string $path       
	 * @param  string|null|mixed $parameters 
	 * @param  string|null|mixed $version    
	 * @param  string|null|mixed $etag       
	 * @return mixed             
	 */
	public function get($path, $parameters  = null, $version = null, $etag = null)
	{
		return $this->api('GET', $path, $parameters, $version, $etag);
	}

	/**
	 * Facebook API Request with "POST" method.
	 * 
	 * @param  string $path       
	 * @param  string|null|mixed $parameters 
	 * @param  string|null|mixed $version    
	 * @param  string|null|mixed $etag       
	 * @return mixed             
	 */
	public function post($path, $parameters  = null, $version = null, $etag = null)
	{
		return $this->api('POST', $path, $parameters, $version, $etag);
	}

	/**
	 * Facebook API Request with "DELETE" method.
	 * 
	 * @param  string $path       
	 * @param  string|null|mixed $parameters 
	 * @param  string|null|mixed $version    
	 * @param  string|null|mixed $etag       
	 * @return mixed             
	 */
	public function delete($path, $parameters  = null, $version = null, $etag = null)
	{
		return $this->api('DELETE', $path, $parameters, $version, $etag);
	}

	/**
	 * Facebook API Request with "PUT" method.
	 * 
	 * @param  string $path       
	 * @param  string|null|mixed $parameters 
	 * @param  string|null|mixed $version    
	 * @param  string|null|mixed $etag       
	 * @return mixed             
	 */
	public function put($path, $parameters  = null, $version = null, $etag = null)
	{
		return $this->api('PUT', $path, $parameters, $version, $etag);
	}

	/**
	 * Facebook API Request with "PATCH" method.
	 * 
	 * @param  string $path       
	 * @param  string|null|mixed $parameters 
	 * @param  string|null|mixed $version    
	 * @param  string|null|mixed $etag       
	 * @return mixed             
	 */
	public function patch($path, $parameters  = null, $version = null, $etag = null)
	{
		return $this->api('PATCH', $path, $parameters, $version, $etag);
	}

	/**
	 * Get user profile.
	 * 
	 * @return mixed 
	 */
	public function getProfile()
	{
		return $this->get('/me');
	}
}