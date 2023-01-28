<?php

namespace FoamyCastle\UUID;

use FoamyCastle\UUID\Prototype\UUID;
use FoamyCastle\UUID\Enum\UUIDVersion;

class TimeBasedUUID extends UUID {
	/**
	 * The number of 100ns periods between 15-Oct-1582 and 1-Jan-1970
	 */
	private const GREGORIAN_TO_UNIX=122192928000000000;
	/**
	 * The maximum number of UUID string that can be generated in a given timestamp window
	 */
	private const COUNTER_MAX=0x1FFF;
	/**
	 * @var int The current timestamp measured in 1µs periods
	 */
	private int $timestamp;
	/**
	 * @var int counter used in the version 1 strings
	 */
	private int $counter;
	function __construct(string $existingNode="") {
		$this->version=UUIDVersion::VERSION_1;
		$this->setTimestamp();
		$this->setNode($existingNode);
		$this->generate();
	}
	function generate(): string {
		if($this->isTimestampTheSame()){
			//unlikely, but if php is quick enough to be able to generate 2 IDs in the same 1µs period,
			//collisions are prevented by incrementing the counter.
			$this->counter++;
		}else{
			//if the a new timestamp period is detected, reset the counter
			$this->resetCounter();
		}
		$this->setTimestamp();
		return $this->uuid = $this->compile();
	}
	protected function getTimestamp():int{
		return (
			self::GREGORIAN_TO_UNIX +
			(int)floor(microtime(true)*10000000)
		);
	}
	protected function getCounter():int{
		return $this->counter;
	}
	protected function getTimeLow(): void {
		$this->components['timeLow'] = sprintf("%'.08x", $this->timestamp & 0xffffffff);
	}
	protected function getTimeMid(): void {
		$this->components['timeMid'] = sprintf("%'.04x", ($this->timestamp >> 32) & 0xffff);
	}
	protected function getTimeHighAndVersion(): void {
		$this->components['timeHigh'] = sprintf(
			"%'.04x",
			(($this->timestamp >> 48) & 0x0fff) | (($this->getVersion() << 12) & 0xf000)
		);
	}
	protected function getClockLow(): void {
		$this->components['ClockLow'] = sprintf("%'.02x", ($this->getCounter() & 0xff));
	}
	protected function getClockHighAndReserved(): void {
		$this->components['clockHigh'] = sprintf(
			"%'.01x",
			(($this->getCounter() >> 8) & 0x1F) | (($this->variant << 5) & 0xe0)
		);
	}
	protected function setTimestamp():void{
		$this->timestamp=$this->getTimestamp();
	}
	private function isTimestampTheSame():bool{
		return $this->timestamp==$this->getTimestamp();
	}
	private function resetCounter():void{
		$this->counter=0;
	}

}