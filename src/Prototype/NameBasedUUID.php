<?php

namespace FoamyCastle\UUID\Prototype;

class NameBasedUUID extends UUID {
	function __construct(string $namespace,string $namespaceID){

		$this->setHashAlgorithm();
		$this->setNamespace($namespace);
		$this->setNamespaceID($namespaceID);
		$this->generate();
	}
	function generate(): string {
		return $this->uuid = $this->compile();
	}
}