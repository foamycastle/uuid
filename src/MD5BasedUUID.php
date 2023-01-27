<?php

namespace FoamyCastle\UUID;

use FoamyCastle\UUID\Enum\UUIDVersion;
use FoamyCastle\UUID\Prototype\NameBasedUUID;

class MD5BasedUUID extends NameBasedUUID {
	function __construct(string $namespace, string $namespaceID) {
		$this->version=UUIDVersion::VERSION_3;
		parent::__construct($namespace, $namespaceID);
	}
}