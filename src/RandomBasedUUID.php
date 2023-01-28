<?php

namespace FoamyCastle\UUID;

use FoamyCastle\UUID\Enum\UUIDVersion;
use FoamyCastle\UUID\Prototype\UUID;

class RandomBasedUUID extends UUID {
	public function __construct() {
		$this->version=UUIDVersion::VERSION_4;
		$this->setNode();
		$this->generate();
	}

	function generate(): string {
		return $this->uuid = $this->compile();
	}
	protected function getTimeLow(): void {
		$this->components['timeLow'] = $this->getARandomHexValue(8);
	}
	protected function getTimeMid(): void {
		$this->components['timeMid']= $this->getARandomHexValue(4);
	}
	protected function getTimeHighAndVersion(): void {
		$this->components['timeHigh']= $this->getVersion() . $this->getARandomHexValue(3);
	}
	protected function getClockLow(): void {
		$this->components['ClockLow']= $this->getARandomHexValue(2);
	}
	protected function getClockHighAndReserved(): void {
		$this->components['clockHigh'] = $this->getARandomHexValue(2);
	}
}