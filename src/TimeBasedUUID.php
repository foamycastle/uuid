<?php

namespace FoamyCastle\UUID;

use FoamyCastle\UUID\Prototype\UUID;
use FoamyCastle\UUID\Enum\UUIDVersion;

class TimeBasedUUID extends UUID {
	function __construct(string $existingNode="") {
		$this->version=UUIDVersion::VERSION_1;
		$this->setNode($existingNode);
		$this->generate();
	}

	function generate(): string {
		return $this->uuid = $this->compile();
	}
}