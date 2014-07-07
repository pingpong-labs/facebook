<?php namespace Pingpong\Facebook;

use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	public function boot()
	{
		$this->package('pingpong/facebook');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['pingpong.facebook'] = $this->app->share(function($app)
		{
			$appId 		= $app['config']->get('facebook::app_id');
			$appSecret 	= $app['config']->get('facebook::app_secret');

			return new Facebook($app, $appId, $appSecret);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('pingpong.facebook');
	}

}
