<?php namespace Pingpong\Facebook;

use Illuminate\Support\ServiceProvider;

class FacebookServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the package.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = config_path('facebook.php');

        $this->publishes([
            __DIR__ . '/../../config/config.php' => $configPath
        ]);

        $this->mergeConfigFrom($configPath, 'facebook');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['pingpong.facebook'] = $this->app->share(function ($app) {
            $config = $app['config']->get('facebook');

            return new Facebook(
                $app['session.store'],
                $app['redirect'],
                $app['config'],
                $app['request'],
                $config['app_id'],
                $config['app_secret'],
                $config['redirect_url']
            );
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
