<?php

namespace Sanovskiy\CodeProfiler;

use Sanovskiy\CodeProfiler\StatePoint;

interface LoggerInterface
{
    /**
     * @param string $sessionID
     * @param int $index
     * @param StatePoint $statePoint
     * @return bool
     */
    public function logPoint(string $sessionID, int $index, StatePoint $statePoint);

    /**
     * @param string $sessionID
     * @param float $startTime
     * @param float $endTime
     * @param int $startMemory
     * @param int $endMemory
     * @param int $peakMemory
     * @return bool
     */
    public function logSession(string $sessionID, float $startTime, float $endTime, int $startMemory, int $endMemory, int $peakMemory);
}