<?php

use App\Application;
use matpoppl\SmallMVC\Utils;

$tm = microtime(true);
$mem = memory_get_usage();

require __DIR__ . '/app/vendor/autoload.php';

$path = __DIR__ . '/app/var/logs/debug.json';
$path = 'php://memory';
$logger = new Utils\Logger(new Utils\RenderWriter($path));

Utils\Debugger::getInstance()->register(-1)->setInit($tm, $mem)->setLogger($logger);

$app = Application::create(require __DIR__ . '/app/configs/app.php');

$app->getContainer()->set('logger.debug', $logger);

$app->run();
