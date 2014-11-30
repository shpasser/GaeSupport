<?php

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Syslog handler is used here to be able to log under Google App Engine.
|
*/

use Monolog\Logger;

$monolog = Log::getMonolog();
$monolog->pushHandler(new Monolog\Handler\SyslogHandler('intranet', 'user',
                      									Logger::DEBUG, false, LOG_PID));
