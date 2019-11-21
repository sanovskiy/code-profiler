<?php

namespace Sanovskiy\CodeProfiler;

use Sanovskiy\CodeProfiler\LoggerInterface;

class Profiler
{
    /**
     * @var
     */
    protected static $instance;
    /**
     * @var float
     */
    protected $startTime;

    /**
     * @var StatePoint[]
     */
    protected $statePoints = [];

    /**
     * @var int
     */
    protected $startMemory;

    /**
     * @var string
     */
    protected $sessionID;

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    protected function __construct()
    {
        $this->sessionID = uniqid('', true);
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage();

        $this->statePoint('Init');
    }

    public function __destruct()
    {
        if ($this->logger instanceof LoggerInterface) {
            if ($this->logger->logSession($this->sessionID, $this->startTime, microtime(true), $this->startMemory, memory_get_usage(), memory_get_peak_usage())) {
                foreach ($this->statePoints as $index => $statePoint) {
                    $this->logger->logPoint($this->sessionID, $index, $statePoint);
                }
            }
        }
    }

    /**
     * @return Profiler
     */
    public static function getInstance(): Profiler
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string|null $label
     * @return StatePoint
     */
    public function statePoint(string $label = null): StatePoint
    {
        $point = new StatePoint($label);
        $point->setTrace(debug_backtrace());
        $point->setMemory(memory_get_usage());
        if (count($this->statePoints) > 0) {
            $point->setPrevPoint($this->statePoints[count($this->statePoints) - 1]);
        }
        $this->statePoints[] = $point;

        return $point;
    }

    /**
     * @return float
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }

    /**
     * @return int
     */
    public function getStartMemory(): int
    {
        return $this->startMemory;
    }

    /**
     * @param bool $addTrace
     * @return array
     */
    public function getStats(bool $addTrace = true): array
    {
        return [
            'total_time' => microtime(true) - $this->startTime,
            'max_memory' => memory_get_peak_usage(),
            'points'     => array_map(function (StatePoint $v) use ($addTrace) { return $v->toArray($addTrace); }, $this->statePoints),
        ];
    }

    /**
     * @param LoggerInterface $logger
     * @return Profiler
     */
    public function setLogger(LoggerInterface $logger): Profiler
    {
        $this->logger = $logger;

        return $this;
    }
}