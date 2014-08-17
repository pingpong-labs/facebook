<?php namespace Pingpong\Facebook\Sdk;

use Illuminate\Http\Request;
use Facebook\FacebookRedirectLoginHelper;
use Illuminate\Session\Store as LaravelSession;

class RedirectLoginHelper extends FacebookRedirectLoginHelper {
	
	protected $request;

	protected $session;

	protected $redirectUrl;

	protected $appId;

	protected $appSecret;
	
	/**
	* @var string Prefix to use for session variables
	*/
	private $sessionPrefix = 'FBRLH_';

	public function __construct($redirectUrl, $appId = null, $appSecret = null, Request $request = null, LaravelSession $session = null)
	{
		$this->appId = Session::_getTargetAppId($appId);
		$this->appSecret = Session::_getTargetAppSecret($appSecret);
		$this->redirectUrl = $redirectUrl;
		$this->request = $request;
		$this->session = $session;
	}

	public function setRequest(Request $request)
	{
		$this->request = $request;
		
		return $this;
	}

	public function getRequest()
	{
		return $this->request;
	}

	public function setSession(LaravelSession $session)
	{
		$this->session = $session;
		
		return $this;
	}

	public function getSession()
	{
		return $this->session;
	}

  	protected function isValidRedirect()
  	{
  		return $this->getCode() && $this->hasState() && $this->isValidSate();
  	}

  	protected function getCode()
  	{
  		return $this->request->get('code');
  	}

  	public function getState()
  	{
  		return $this->request->get('state');
  	}

  	public function hasState()
  	{
  		return $this->request->has('state');
  	}

  	public function isValidSate()
  	{
  		return $this->getState() == $this->state;
  	}
  	
  	public function getSessionStateName()
  	{
  		return $this->sessionPrefix . 'state';
  	}

  	protected function storeState($state)
	{
		$this->session->put($this->getSessionStateName(), $state);
	}
  
  	protected function loadState()
  	{
    	$sessionKey = $this->getSessionStateName;

    	if($this->session->has($sessionKey))
    	{
    		$this->state = $this->session->get($sessionKey);

    		return $this->state;
    	}

    	return null;
  	}

} 