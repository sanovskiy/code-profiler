<?php
include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Sanovskiy\CodeProfiler\FileLogger;
use Sanovskiy\CodeProfiler\Profiler;


Profiler::getInstance()->statePoint('Before sleep');
sleep(1);
Profiler::getInstance()->statePoint('After sleep');

for ($ll = 0; $ll < 5; $ll++) {
    $array = [];
    Profiler::getInstance()->statePoint('Before array fill ' . $ll);
    for ($ii = 0; $ii < 1000000; $ii++) {
        $array[] = 'Just string. Nothing more. ' . uniqid('', true);
    }
    Profiler::getInstance()->statePoint('After array fill ' . $ll);

    unset($array);
    Profiler::getInstance()->statePoint('After array unset ' . $ll);
}
Profiler::getInstance()->setLogger(new FileLogger(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logfile.log'));

print_r(Profiler::getInstance()->getStats(false));