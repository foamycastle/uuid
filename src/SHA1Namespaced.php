<?php

namespace FoamyCastle\UUID;

class SHA1Namespaced extends UUID
{
    private const VERSION=5;
    private const VARIANT=8;
    private const HASH_ALGO='sha1';

    private string $namespace;
    private string $hash;
    protected function __construct(string $namespace)
    {
        $this->namespace=$namespace;
        $this->hash=hash(self::HASH_ALGO,$this->namespace);
    }
    public function setNamespace(string $namespace):static
    {
        $this->namespace=$namespace;
        $this->hash=hash(self::HASH_ALGO,$this->namespace);
        return $this;
    }
    public function __toString(): string
    {
        $this->previous=sprintf(
            "%s-%s-%s-%s-%s",
            substr($this->hash,0,8),
            substr($this->hash,8,4),
            (string)$this->getVersion().substr($this->hash,13,3),
            (string)$this->getReserved().substr($this->hash,17,3),
            substr($this->hash,20,12)
        );
        return strtoupper($this->previous);
    }

    protected function getTimeLow(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    protected function getTimeMid(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    protected function getTimeHigh(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    protected function getVersion(): int
    {
        return self::VERSION;
    }

    /**
     * @inheritDoc
     */
    protected function getClockSequence(): int
    {
        return 0;
    }

    /**
     * @inheritDoc
     */
    protected function getReserved(): int
    {
        return self::VARIANT;
    }

    /**
     * @inheritDoc
     */
    protected function getNode()
    {
        return null;
    }
}