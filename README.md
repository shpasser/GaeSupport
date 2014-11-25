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

### Mail

The mail driver is configuration file can be found at `app/config/production/mail.php`,
it is one of the many configuration files generated by the artisan command. There is
no need in any kind of custom configuration. All the outgoing mail messages are sent
with sender address of an administrator of the application, i.e. `admin@your-app-id.appspotmail.com`.
The work is in progress, 'sender', 'to', 'subject' and 'body' parts of the email message
are supported now. When finished will support the fields supported by both Swift Mailer 
and Google App engine. 

### Queues

Documentation is in progress, please stay tuned!

## Deploy

Download and install GAE SDK for PHP and deploy your app.