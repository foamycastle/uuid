<?php

namespace FoamyCastle\UUID;

use FoamyCastle\UUID\Exceptions\InvalidNamespaceIDValueException;
use FoamyCastle\UUID\Exceptions\UnVersionedException;

class NameBasedUUID extends UUID {
	/**
	 * @var string namespace for version 3 and version 5 UUID strings
	 */
	protected string $namespace;
	/**
	 * @var string the UUID string that is used for version 3 and version 5 generation
	 */
	protected string $namespaceID;
	/**
	 * @var array|string[] hash algorithms used in version 3 and version 5
	 */
	protected array $hashAlgorithms=[
		3=>'MD5',
		5=>'sha1'
	];
	/**
	 * @var string|mixed the chosen hash algorithm for the generator
	 */
	protected string $hashAlgorithm;
	/**
	 * @var string a hash string generated for version 3 and version 5
	 */
	protected string $hash;
	function __construct(string $namespace,string $namespaceID){

		$this->setHashAlgorithm();
		$this->setNamespace($namespace);
		$this->setNamespaceID($namespaceID);
		$this->generate();
	}
	function generate(): string {
		$this->performHash();
		$this->setNode(substr($this->hash,20,12));
		return $this->uuid = $this->compile();
	}

	protected function getTimeLow(): void {
		$this->components['timeLow'] = substr($this->hash, 0, 8);
	}

	protected function getTimeMid(): void {
		$this->components['timeMid'] = substr($this->hash, 8, 4);
	}

	protected function getTimeHighAndVersion(): void {
		$this->components['timeHigh'] = sprintf(
			"%x%s",
			$this->getVersion(),
			substr($this->hash, 13, 3)
		);
	}

	protected function getClockLow(): void {
		$this->components['ClockLow'] = substr($this->hash, 18, 2);
	}

	protected function getClockHighAndReserved(): void {
		$this->components['clockHigh'] = sprintf(
			"%x%s",
			(($this->variant << 1) & 0xe) | (hexdec(substr($this->hash, 16, 1)) & 0x1),
			substr($this->hash, 17, 1)
		);
	}
	public function setNamespaceID(string $value):static {
		if(!$this->validateNamespaceID($value)){
			throw new InvalidNamespaceIDValueException();
		}
		$this->namespaceID = str_replace('-', "", $value);

		return $this;
	}
	public function setNamespace(string $value):static {
		$this->namespace=$value;
		return $this;
	}
	private function setHashAlgorithm():void{
		if(!$this->isVersioned()){
			throw new UnVersionedException();
		}
		$this->hashAlgorithm=$this->hashAlgorithms[$this->version->value];
	}
	public function getNamespaceID():string{
		return $this->namespaceID ?? $this->namespaceID=str_repeat($this->getARandomHexValue(8),4);
	}
	public function getNamespace():string{
		return $this->namespace ?? $this->namespace=base64_encode(random_bytes(6));
	}
	private function validateNamespaceID(string $value):bool{
		return preg_match(self::UUID_FORMAT_VALID,$value)==1;
	}
	private function performHash():void {
		$this->hash=openssl_digest(
			$this->getNamespaceID() . $this->getNamespace(),
			$this->hashAlgorithm
		);
	}
	private function isVersioned():bool{
		return isset($this->version);
	}
}