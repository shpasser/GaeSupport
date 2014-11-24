<?php namespace Shpasser\GaeSupport\Queue;

use Illuminate\Http\Request;
use Illuminate\Encryption\Encrypter;
use Illuminate\Queue\Connectors\ConnectorInterface;

class GaeConnector implements ConnectorInterface {

	/**
	 * The encrypter instance.
	 *
	 * @var \Illuminate\Encryption\Encrypter
	 */
	protected $crypt;

	/**
	 * The current request instance.
	 *
	 * @var \Illuminate\Http\Request;
	 */
	protected $request;


	/**
	 * Create a new GAE connector instance.
	 *
	 * @param \Illuminate\Encryption\Encrypter $crypt
	 * @param \Illuminate\Http\Request $request
	 */
	public function __construct(Encrypter $crypt, Request $request)
	{
		$this->crypt = $crypt;
		$this->request = $request;
	}

	/**
	 * Establish a queue connection.
	 *
	 * @param  array  $config
	 * @return \Illuminate\Queue\QueueInterface
	 */
	public function connect(array $config)
	{
		return new GaeQueue($this->request, $config['queue'], $config['url'], $config['encrypt']);
	}

}
