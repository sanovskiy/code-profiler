<?php

namespace Sanovskiy\CodeProfiler;

use SplFileObject;

/**
 * Class FileLogger
 * @package Sanovskiy\CodeProfiler
 */
class FileLogger implements LoggerInterface
{
    const CHAR_TAB = '    ';
    /**
     * @var SplFileObject
     */
    protected $file;

    public function __construct(string $logfile)
    {
        if ((file_exists($logfile) && !is_writable($logfile)) || (!file_exists($logfile) && !is_writable(dirname($logfile)))) {
            throw new \RuntimeException(sprintf('Logfile %s is not writable', $logfile));
        }
        $this->file = new SplFileObject($logfile, 'ab');
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