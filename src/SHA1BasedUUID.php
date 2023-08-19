<?php

namespace FoamyCastle\UUID;
use FoamyCastle\UUID\Enum\UUIDVersion;

class SHA1BasedUUID extends NameBasedUUID {
	function __construct(string $namespace, string $namespaceID) {
		$this->version=UUIDVersion::VERSION_5;
		parent::__construct($namespace, $namespaceID);
	}
}