<?php

namespace FoamyCastle\UUID;

use FoamyCastle\UUID\UUID;

class Random extends UUID
{
    private const VERSION=4;
    private const VARIANT=4;
    protected function __construct()
    {

    }
    /**
     * @inheritDoc
     */
    protected function getTimeLow(): int
    {
        return $this->randomBits(32);
    }

    /**
     * @inheritDoc
     */
    protected function getTimeMid(): int
    {
        return $this->randomBits(16);
    }

    /**
     * @inheritDoc
     */
    protected function getTimeHigh(): int
    {
        return $this->randomBits(12);
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
    protected function getNode()
    {
        return $this->randomBits(48);
    }
}