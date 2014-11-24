<?php namespace Shpasser\GaeSupport\Mail;

use Shpasser\GaeSupport\Mail\Transport\GaeTransport;
use Illuminate\Mail\MailServiceProvider;

class GaeMailServiceProvider extends MailServiceProvider {

	/**
	 * Register the Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function registerSwiftTransport($config)
	{
		switch ($config['driver'])
		{
			case 'gae':
				if (! $this->app->isRunningOnGae()) {
					throw new \InvalidArgumentException('Cannot use GaeMailProvider if '.
					                                    'not running on Google App Engine.');
				}
				return $this->registerGaeTransport($config);

			default:
				return parent::registerSwiftTransport($config);
		}
	}

	/**
	 * Register the GAE Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerGaeTransport($config)
	{
		$this->app->bindShared('swift.transport', function()
		{
			return new GaeTransport($this->app);
		});
	}

}
