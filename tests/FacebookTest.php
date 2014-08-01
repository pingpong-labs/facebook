<?php

session_start();

use Mockery as m;
use Pingpong\Facebook\Facebook;

class FacebookTest extends PHPUnit_Framework_TestCase
{
    protected $session;
    protected $redirect;
    protected $config;
    protected $appId;
    protected $appSecret;
    protected $redirect_url = '/';

    function setUp()
    {
        $this->session = m::mock('Illuminate\Session\Store');
        $this->redirect = m::mock('Illuminate\Routing\Redirector');
        $this->config = m::mock('Illuminate\Config\Repository');
        $this->appId = 'appid';
        $this->appSecret = 'secret';

        $this->facebook = new Facebook(
            $this->session,
            $this->redirect,
            $this->config,
            $this->appId,
            $this->appSecret,
            $this->redirect_url
        );
    }

    function tearDown()
    {
        m::close();
    }

    function testGetRedirectUrl()
    {
        $redirectUrl = $this->facebook->getRedirectUrl();

        $this->assertEquals('/', $redirectUrl);
    }

    function testGetFacebookLoginHelper()
    {
        $facebookLoginHelper = $this->facebook->getFacebookHelper();

        $this->assertInstanceOf('Facebook\FacebookRedirectLoginHelper', $facebookLoginHelper);
    }

    function testGetLoginUrl()
    {
        $this->facebook->setRedirectUrl(null);

        $this->config->shouldReceive('get')->once()->with('facebook::redirect_url', '/')->andReturn('foo');
        $this->config->shouldReceive('get')->once()->with('facebook::scope')->andReturn([]);

        $loginUrl = $this->facebook->getLoginUrl();

        $this->assertTrue(is_string($loginUrl));
    }

    function testAuthentication()
    {
        $this->facebook->setRedirectUrl(null);
        
        $this->config->shouldReceive('get')->once()->with('facebook::redirect_url', '/')->andReturn('foo');
        $this->config->shouldReceive('get')->once()->with('facebook::scope')->andReturn([]);

        $this->redirect->shouldReceive('to')->once();

        $actual = $this->facebook->authenticate();

        $this->assertNull($actual);
    }
}
