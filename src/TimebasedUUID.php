<?php

namespace FoamyCastle\UUID;

class TimebasedUUID extends UUID
{
    private const VERSION=1;
    private const VARIANT=4;
    protected function __construct()
    {
        $this->setTimestamp();
    }
    /**
     * @inheritDoc
     */
    protected function getTimeLow(): int
    {
        return $this->timestamp & 0xFFFFFFFF;
    }

    /**
     * @inheritDoc
     */
    protected function getTimeMid(): int
    {
        return ($this->timestamp>>32) & 0xFFFF;
    }

    /**
     * @inheritDoc
     */
    protected function getTimeHigh(): int
    {
        return (($this->timestamp>>48) & 0x0FFF);
    }
    /**
     * @inheritDoc
     */
    protected function getVersion(): int
    {
        return self::VERSION<<12;
    }

    /**
     * @inheritDoc
     */
    protected function getClockSequence(): int
    {
        return $this->randomBits(13);
    }

    /**
     * @inheritDoc
     */
    protected function getReserved(): int
    {
        return self::VARIANT<<13;
    }

    /**
     * @inheritDoc
     */
    protected function getNode():int
    {
        if(empty($this->node)) return $this->randomBits(48);
        return $this->node;
    }
}