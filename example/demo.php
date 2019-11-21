<?php
include dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Sanovskiy\CodeProfiler\Profiler;
use Sanovskiy\CodeProfiler\StatePoint;

/**
 * Class MyLogger
 * @noinspection AutoloadingIssuesInspection
 */
class MyLogger implements \Sanovskiy\CodeProfiler\LoggerInterface
{
    const CHAR_TAB = "\t";
    /**
     * @var SplFileObject
     */
    protected $file;

    public function __construct()
    {
        $this->file = new SplFileObject(__DIR__ . DIRECTORY_SEPARATOR . 'logfile.log', 'ab');
    }

    public function __destruct()
    {
        $this->file = null;
    }

    /**
     * @param string $sessionID
     * @param int $index
     * @param StatePoint $statePoint
     * @return mixed
     */
    public function logPoint(string $sessionID, int $index, StatePoint $statePoint)
    {
        $this->file->fwrite(implode(self::CHAR_TAB, [
                '[STATE POINT]',
                $sessionID,
                date('Y-m-d H:i:s', $statePoint->getTime()),
                $statePoint->getTimeDelta() ?? ' ',
                $statePoint->getLabel(),
                $statePoint->getMemory(),
                $statePoint->getMemotyDelta() ?? 0,
                $statePoint->getTrace(0)['file'] . ':' . $statePoint->getTrace(0)['line'],
                json_encode($statePoint->toArray(false)),
            ]) . PHP_EOL);

        return true;
    }

    /**
     * @param string $sessionID
     * @param float $startTime
     * @param float $endTime
     * @param int $startMemory
     * @param int $endMemory
     * @param int $peakMemory
     * @return mixed
     */
    public function logSession(string $sessionID, float $startTime, float $endTime, int $startMemory, int $endMemory, int $peakMemory)
    {
        $this->file->fwrite(implode(self::CHAR_TAB, [
                '[SESSION]',
                $sessionID,
                date('Y-m-d H:i:s', $startTime),
                date('Y-m-d H:i:s', $endTime),
                $startMemory,
                $endMemory,
                $peakMemory,
            ]) . PHP_EOL);

        return true;
    }
}

Profiler::getInstance()->setLogger(new MyLogger());

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
print_r(Profiler::getInstance()->getStats(false));