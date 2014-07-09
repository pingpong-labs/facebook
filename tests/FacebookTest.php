<?php

session_start();

use Mockery as m;
use Pingpong\Facebook\Facebook;

class FacebookTest extends PHPUnit_Framework_TestCase
{
    function testGetRedirectUrl()
    {
        $session = m::mock('Illuminate\Session\Store');
        $redirect = m::mock('Illuminate\Routing\Redirector');
        $config = m::mock('Illuminate\Config\Repository');
        $appId    = 'appid';
        $appSecret = 'secret';

        $facebook = new Facebook($session, $redirect, $config, $appId, $appSecret, '/');

        $redirectUrl = $facebook->getRedirectUrl();

        $this->assertEquals('/', $redirectUrl);
    }

    function testGetFacebookLoginHelper()
    {
        $session = m::mock('Illuminate\Session\Store');
        $redirect = m::mock('Illuminate\Routing\Redirector');
        $config = m::mock('Illuminate\Config\Repository');
        $appId    = 'appid';
        $appSecret = 'secret';

        $facebook = new Facebook($session, $redirect, $config, $appId, $appSecret, '/');

        $facebookLoginHelper = $facebook->getFacebookHelper();

        $this->assertInstanceOf('Facebook\FacebookRedirectLoginHelper', $facebookLoginHelper);
    }

    function testGetLoginUrl()
    {
        $session = m::mock('Illuminate\Session\Store');
        $redirect = m::mock('Illuminate\Routing\Redirector');
        $config = m::mock('Illuminate\Config\Repository');
        $appId    = 'appid';
        $appSecret = 'secret';

        $facebook = new Facebook($session, $redirect, $config, $appId, $appSecret, '/');

        $config->shouldReceive('get')->once()->with('facebook::app_id')->andReturn('foo');
        $config->shouldReceive('get')->once()->with('facebook::app_secret')->andReturn('bar');
        $config->shouldReceive('get')->once()->with('facebook::scope')->andReturn([]);

        $loginUrl = $facebook->getLoginUrl();

        $this->assertTrue(is_string($loginUrl));
    }
}