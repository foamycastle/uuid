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
}