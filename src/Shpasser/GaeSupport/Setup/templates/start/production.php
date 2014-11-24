<?php

use Monolog\Logger;

$monolog = Log::getMonolog();
$monolog->pushHandler(new Monolog\Handler\SyslogHandler('intranet', 'user',
                      									Logger::DEBUG, false, LOG_PID));
