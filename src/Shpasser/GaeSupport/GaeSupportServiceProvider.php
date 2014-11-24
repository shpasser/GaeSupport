<?php namespace Shpasser\GaeSupport;

use Illuminate\Support\ServiceProvider;
use Shpasser\GaeSupport\Setup\SetupCommand;

class GaeSupportServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['gae.setup'] = $this->app->share(function($app)
		{
			return new SetupCommand;
		});

		$this->commands('gae.setup');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('gae-support');
	}

}
