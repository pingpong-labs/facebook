<?php

putenv('FACEBOOK_TESTING=1');

use Illuminate\Http\Request;
use Mockery as m;
use Pingpong\Facebook\Facebook;

class FacebookTest extends PHPUnit_Framework_TestCase
{

    protected $session;

    protected $redirect;

    protected $config;

    protected $request;

    protected $appId;

    protected $appSecret;

    protected $redirect_url = '/';

    public function setUp()
    {
        $this->session = m::mock('Illuminate\Session\Store');
        $this->redirect = m::mock('Illuminate\Routing\Redirector');
        $this->config = m::mock('Illuminate\Config\Repository');
        $this->request = m::mock('Illuminate\Http\Request');
        $this->appId = 'appid';
        $this->appSecret = 'secret';

        $this->facebook = new Facebook(
            $this->session,
            $this->redirect,
            $this->config,
            $this->request,
            $this->appId,
            $this->appSecret,
            $this->redirect_url
        );
    }

    public function tearDown()
    {
        m::close();
    }

    public function testGetRedirectUrl()
    {
        $redirectUrl = $this->facebook->getRedirectUrl();

        $this->assertEquals('/', $redirectUrl);
    }

    public function testGetFacebookLoginHelper()
    {
        $facebookLoginHelper = $this->facebook->getFacebookHelper();

        $this->assertInstanceOf('Facebook\FacebookRedirectLoginHelper', $facebookLoginHelper);
    }

    public function getDummyUrlGenerator()
    {
        return new Illuminate\Routing\UrlGenerator(
            new Illuminate\Routing\RouteCollection,
            new Request
        );
    }

    public function testGetLoginUrl()
    {
        $this->facebook->setRedirectUrl(null);

        $this->config->shouldReceive('get')->once()->with('facebook.redirect_url', '/')->andReturn('foo');
        $this->config->shouldReceive('get')->once()->with('facebook.scope')->andReturn([]);
        $this->redirect->shouldReceive('getUrlGenerator')
            ->once()
            ->andReturn($this->getDummyUrlGenerator());

        $loginUrl = $this->facebook->getLoginUrl();

        $this->assertTrue(is_string($loginUrl));
    }

    public function testAuthentication()
    {
        $this->facebook->setRedirectUrl(null);

        $this->config->shouldReceive('get')->once()->with('facebook.redirect_url', '/')->andReturn('foo');
        $this->config->shouldReceive('get')->once()->with('facebook.scope')->andReturn([]);
        $this->redirect->shouldReceive('getUrlGenerator')
            ->once()
            ->andReturn($this->getDummyUrlGenerator());

        $this->redirect->shouldReceive('to')->once();

        $actual = $this->facebook->authenticate();

        $this->assertNull($actual);
    }
}
