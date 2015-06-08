# GaeSupport

Google App Engine(GAE) Support package for Laravel 4.

Currently supported features:
- Generation of general configuration files,
- Mail service provider,
- Queue service provider.

For Laravel 5 see https://github.com/shpasser/GaeSupportL5.  
For Lumen see https://github.com/shpasser/GaeSupportLumen.

## Installation

Pull in the package via Composer.

```js
"require": {
    "shpasser/gae-support": "~1.0"
}
```

Then include the service provider within `app/config/app.php`.

```php
'providers' => [
    'Shpasser\GaeSupport\GaeSupportServiceProvider'
];
```

## Usage

Generate the GAE related files / entries.

```bash
php artisan gae:setup --config your-app-id
```

The default GCS bucket is configured unless a custom bucket id is defined using
the `--bucket` option.

```bash
php artisan gae:setup --config --bucket="your-bucket-id" your-app-id
```

### Mail

The mail driver configuration file can be found at `app/config/production/mail.php`,
it is one of many configuration files generated by the artisan command. There is
no need in any kind of custom configuration. All the outgoing mail messages are sent
with sender address of an administrator of the application, i.e. `admin@your-app-id.appspotmail.com`.
The `sender`, `to`, `cc`, `bcc`, `replyTo`, `subject`, `body` and `attachment` 
parts of email message are supported. 

### Queues

The generated queue configuration file `app/config/production/queue.php` should contain:

```php
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
```

The 'default' queue and encryption are used by default. 
In order to use the queue your `app/routes.php` file should contain the following route:

```php
Route::post('tasks', array('as' => 'tasks',
function()
{
	return Queue::marshal();
}));
```
  
This route will be used by the GAE queue to push the jobs. Please notice that the route
and the GAE Queue Connection 'url' parameter point to the same URL.
For more information on the matter please see http://laravel.com/docs/4.2/queues#push-queues.

## Deploy

Download and install GAE SDK for PHP and deploy your app.
