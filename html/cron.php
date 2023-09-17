<?php

use App\Application;
use matpoppl\SmallMVC\Utils;
use matpoppl\Cron\Runner;

$tm = microtime(true);
$mem = memory_get_usage();

require __DIR__ . '/app/vendor/autoload.php';

$path = __DIR__ . '/app/var/logs/debug.json';
$path = 'php://output';
$logger = new Utils\Logger(new Utils\RenderWriter($path));

Utils\Debugger::getInstance()->register(-1)->setInit($tm, $mem)->setLogger($logger);

$app = Application::create(require __DIR__ . '/app/configs/app.php');

$rountTo = 5 * 60;

$time = time();
$ret = [];
for ($i = 0; $i < 1000; $i++) {
    
    $time += 31;
    
    $ts = $time - ($time % $rountTo);
    
    $date = date('Y-m-d H:i:s', $ts);
    $ret[date('Y-m-d H:i:s', $time)] = $date;
}

var_dump($ret);
die();




$runner = new Runner($app->getContainer());

$runner->run();
