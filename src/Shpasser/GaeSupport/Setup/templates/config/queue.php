<?php

return array(

	'default' => 'gae',

	/*
	|--------------------------------------------------------------------------
	| GAE Queue Connection
	|--------------------------------------------------------------------------
	|
	*/

	'connections' => array(

		'gae' => array(
			'driver'	=> 'gae',
			'queue'		=> 'default',
			'url'		=> '/tasks',
			'encrypt'	=> true,
		),

	),

);
