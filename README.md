# GaeSupport

Google App Engine Support package for Laravel 4.

Currently supported features:
- Generation of general configuration files,
- Mail service provider,
- Queue service provider.


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
 php artisan gae:setup --config `your-app-id`
 ```

### Mail and Queue Service Providers

Documentation is in progress, please stay tuned!

## Deploy

Download and install GAE SDK for PHP and deploy your app.