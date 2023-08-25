<?php

namespace FoamyCastle\UUID;

abstract class UUID
{
    protected const GREGOR=12219292800000000;
    protected int $timestamp;
    protected int $timeLow;
    protected int $timeMid;
    protected int $timeHigh;
    protected int $version;
    protected int $reserved;
    protected int $clocksequence;
    protected int $node;
    public string $previous;

    protected function setTimestamp():void
    {
        $this->timestamp=self::GREGOR+(microtime(true)*(10**7));
    }
    protected function randomBits(int $numberOfBits):int
    {
        return rand(0,(2**$numberOfBits)-1);
    }
    public function __toString(): string
    {
        $this->setTimestamp();
        $this->previous=sprintf(
            "%08X-%04X-%04X-%04X-%012X",
            $this->getTimeLow(),
            $this->getTimeMid(),
            $this->getTimeHigh()|$this->getVersion(),
            $this->getClockSequence()|$this->getReserved(),
            $this->getNode()
        );
        return $this->previous;
    }

    /**
     * The first group of the UUID
     * XXXXXXXX-0000-0000-0000-000000000000
     * @return int 32bit number
     */
    abstract protected function getTimeLow():int;

    /**
     * The second group of the UUID<br>
     * 00000000-XXXX-0000-0000-000000000000
     * @return int 16bit number
     */
    abstract protected function getTimeMid(): int;

    /**
     * Forms part of the third group of the UUID<br>
     * 00000000-0000-XXX0-0000-000000000000
     * @return int 12bit number shifted left 4 bits
     */
    abstract protected function getTimeHigh(): int;
    /**
     * Forms part of the third group of the UUID<br>
     * 00000000-0000-000X-0000-000000000000
     * @return int 4bit number
     */
    abstract protected function getVersion(): int;

    /**
     * Forms part of the fourth group of the UUID<br>
     * 00000000-0000-0000-XXX0-000000000000
     * @return int 13bit number
     */
    abstract protected function getClockSequence():int;
    public function setReserved(int $value): static
    {
        if($value<1||$value>7){
            $this->reserved=1;
        }else{
            $this->reserved=$value;
        }
        return $this;
    }

    /**
     * Forms part of the fourth group of the UUID<br>
     * 00000000-0000-0000-000X-000000000000
     * @return int 3bit number
     */
    abstract protected function getReserved():int;
    public function setNode($value): static
    {
        if(is_string($value)){
            $this->node=crc32($value)<<16;
            return $this;
        }
        if(is_int($value)){
            if($value>(2**48)-1){
                $this->node=$value&0xFFFFFFFFFFFF;
                return $this;
            }
            $this->node=$value;
            return $this;
        }
        if(empty($value)){
            $this->node=rand(0,2**48)-1;
        }
        return $this;
    }
    /**
     * Forms the fifth group of the UUID<br>
     * 00000000-0000-0000-0000-XXXXXXXXXXXX
     * @return int 48bit number
     */
    abstract protected function getNode();

    public static function Timebased():TimebasedUUID
    {
        return new TimebasedUUID();
    }

    public static function MD5(string $namespace):MD5Namespaced
    {
        return new MD5Namespaced($namespace);
    }

    public static function SHA1(string $namespace):SHA1Namespaced
    {
        return new SHA1Namespaced($namespace);
    }

    public static function Random():Random
    {
        return new Random();
    }
}