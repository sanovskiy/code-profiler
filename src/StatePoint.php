<?php

namespace Sanovskiy\CodeProfiler;

class StatePoint
{
    /**
     * @var string
     */
    protected $label;

    /**
     * @var array
     */
    protected $trace;

    /**
     * @var float
     */
    protected $time;

    /**
     * @var int
     */
    protected $memory;

    public function __construct(string $label = null)
    {
        $this->label = $label;
        $this->time = microtime(true);
    }

    /**
     * @var StatePoint|null
     */
    protected $prevPoint = null;

    public function setPrevPoint(StatePoint $statePoint)
    {
        $this->prevPoint = $statePoint;
    }

    /**
     * @return StatePoint|null
     */
    public function prevPoint()
    {
        return $this->prevPoint;
    }

    /**
     * @param null $index
     * @return array
     */
    public function getTrace($index = null): array
    {
        if ($index === null) {
            return $this->trace;
        }
        if (array_key_exists($index, $this->trace)) {
            return $this->trace[$index];
        }

        return [];
    }

    /**
     * @param array $trace
     * @return StatePoint
     */
    public function setTrace(array $trace): StatePoint
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * @return float
     */
    public function getTime(): float
    {
        return $this->time;
    }

    /**
     * @return mixed
     */
    public function getMemory()
    {
        return $this->memory;
    }

    public function getMemotyDelta()
    {
        return ($this->prevPoint instanceof self) ? $this->memory - $this->prevPoint->memory : null;
    }

    public function getTimeDelta()
    {
        return ($this->prevPoint instanceof self) ? $this->time - $this->prevPoint->time : null;
    }

    /**
     * @param int $memory
     * @return StatePoint
     */
    public function setMemory(int $memory): StatePoint
    {
        $this->memory = $memory;

        return $this;
    }

    /**
     * @param bool $addTrace
     * @return array
     */
    public function toArray(bool $addTrace = true): array
    {
        return [
            'label'  => $this->label,
            'caller' => array_filter($this->trace[0], function ($key) { return in_array($key, ['file', 'line']); }, ARRAY_FILTER_USE_KEY),
            'time'   => [
                'value'      => $this->time,
                'delta'      => $this->getTimeDelta(),
                'full_delta' => $this->time - Profiler::getInstance()->getStartTime(),
            ],
            'memory' => [
                'value'      => $this->time,
                'delta'      => $this->getMemotyDelta(),
                'full_delta' => $this->memory - Profiler::getInstance()->getStartMemory(),
            ],
            'trace'  => $addTrace ? $this->trace : [],
        ];

    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

}