<?php

namespace FoamyCastle\UUID\Prototype;

use FoamyCastle\UUID\Enum\UUIDVersion;
use FoamyCastle\UUID\Exceptions\UnVersionedException;
use FoamyCastle\UUID\Exceptions\InvalidNodeValueException;
use FoamyCastle\UUID\Exceptions\InvalidNamespaceIDValueException;

abstract class UUID {
	/**
	 * The number of 100ns periods between 15-Oct-1582 and 1-Jan-1970
	 */
	protected const GREGORIAN_TO_UNIX=122192928000000000;
	/**
	 * Regex expression that validates UUID strings
	 */
	protected const UUID_FORMAT_VALID="/^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})|([a-f0-9]{32})$/i";
	/**
	 * The format string used by sprintf() for UUID output
	 */
	private const NODE_FORMAT_VALID="/^[a-f0-9]{12}$/i";
	/**
	 * The maximum number of UUID string that can be generated in a given timestamp window
	 */
	protected const COUNTER_MAX=0x1FFF;
	/**
	 * @var array contains the constituent elements of a UUID string after compilation
	 */
	protected const UUID_FORMAT_OUTPUT="%s-%s-%s-%s%s-%s";
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
	/**
	 * @var int The current timestamp measured in 1µs periods
	 */
	protected int $timestamp;
	/**
	 * Regex expression that validates a user-supplied node ID
	 */
	protected array $components;
	/**
	 * @var string a 48-bit value used as an identifier in version 1 strings
	 */
	protected string $node;
	/**
	 * @var int counter used in the version 1 strings
	 */
	protected int $counter;
	private int $variant=4;
	/**
	 * @var \FoamyCastle\UUID\Enum\UUIDVersion the version of the UUID string
	 */
	protected UUIDVersion $version;
	/**
	 * @var string the most recently generated UUID
	 */
	protected string $uuid;
	function __toString():string{
		return $this->uuid ?? "";
	}
	private function getARandomHexValue(int $nibblesLong, int $multiplex=0):string{
		$minValue="1".str_pad("",$nibblesLong-1,"0");
		$maxValue=str_pad("",$nibblesLong,"F");
		return sprintf("%x",mt_rand(hexdec($minValue),hexdec($maxValue)) | $multiplex);
	}
	public function setNode(?string $value=null):static {
		if(!$value) {
			$this->node = $this->getARandomHexValue(12);
		}elseif($value&&$this->validateNodeValue($value)) {
			$this->node = $value;
		}else{
			throw new InvalidNodeValueException();
		}
		return $this;
	}
	private function setTimestamp():void{
		$this->timestamp=$this->getTimestamp();
	}
	private function getCounter():int{
		return $this->counter;
	}
	private function getTimestamp():int{
		return (
			self::GREGORIAN_TO_UNIX +
			(int)floor(microtime(true)*10000000)
		);
	}
	protected function validateNodeValue(string $value):bool{
		return preg_match(self::NODE_FORMAT_VALID,$value)==1;
	}
	private function getTimeLow():void{
		if ($this->isTimeBased()) {
			$this->components['timeLow'] = sprintf("%'.08x", $this->timestamp & 0xffffffff);
			return;
		}
		if ($this->isNameBased()) {
			$this->components['timeLow'] = substr($this->hash, 0, 8);
			return;
		}
		$this->components['timeLow'] = $this->getARandomHexValue(8);
	}
	private function getTimeMid():void{
		if ($this->isTimeBased()) {
			$this->components['timeMid'] = sprintf("%'.04x", ($this->timestamp >> 32) & 0xffff);
			return;
		}
		if ($this->isNameBased()) {
			$this->components['timeMid'] = substr($this->hash, 8, 4);
			return;
		}
		$this->components['timeMid']= $this->getARandomHexValue(4);
	}
	private function getTimeHighAndVersion():void{
		if ($this->isTimeBased()) {
			$this->components['timeHigh'] = sprintf(
				"%'.04x",
				(($this->timestamp >> 48) & 0x0fff) | (($this->getVersion() << 12) & 0xf000)
			);
			return;
		}
		if ($this->isNameBased()) {
			$this->components['timeHigh'] = sprintf(
				"%x%s",
				$this->getVersion(),
				substr($this->hash, 13, 3)
			);
			return;
		}
		$this->components['timeHigh']= $this->getVersion() . $this->getARandomHexValue(3);
	}
	private function getClockLow():void{
		if ($this->isTimeBased()) {
			$this->components['ClockLow'] = sprintf("%'.02x", ($this->getCounter() & 0xff));
			return;
		}
		if ($this->isNameBased()) {
			$this->components['ClockLow'] = substr($this->hash, 18, 2);
			return;
		}
		$this->components['ClockLow']= $this->getARandomHexValue(2);
	}
	private function getClockHighAndReserved():void{
		if ($this->isTimeBased()) {
			$this->components['clockHigh'] = sprintf(
				"%'.01x",
				(($this->getCounter() >> 8) & 0x1F) | (($this->variant << 5) & 0xe0)
			);
			return;
		}
		if ($this->isNameBased()) {
			$this->components['clockHigh'] = sprintf(
				"%x%s",
				(($this->variant << 1) & 0xe) | (hexdec(substr($this->hash, 16, 1)) & 0x1),
				substr($this->hash, 17, 1)
			);
			return;
		}
		$this->components['clockHigh'] = $this->getARandomHexValue(2);
	}
	public function setNamespaceID(string $value):UUID {
		if($this->isNameBased()){
			if(!$this->validateNamespaceID($value)){
				throw new InvalidNamespaceIDValueException();
			}
			$this->namespaceID = str_replace('-', "", $value);
		}
		return $this;
	}
	public function setNamespace(string $value):UUID {
		if($this->isNameBased()){
			$this->namespace=$value;
		}
		return $this;
	}
	protected function setHashAlgorithm():void{
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
	protected function validateNamespaceID(string $value):bool{
		return preg_match(self::UUID_FORMAT_VALID,$value)==1;
	}
	private function performHash():void {
		$this->hash=openssl_digest(
			$this->getNamespaceID() . $this->getNamespace(),
			$this->hashAlgorithm
		);
	}
	public function getNode():string {
		$this->components['nodeValue']=$this->node;
		return $this->node ?? "";
	}
	private function isTimestampTheSame():bool{
		return $this->timestamp ?? 0==$this->getTimestamp();
	}
	private function resetCounter():void{
		$this->counter=0;
	}
	private function resetComponents():void{
		$this->components=[];
	}
	private function isNameBased():bool{
		return $this->version==UUIDVersion::VERSION_3||
			$this->version==UUIDVersion::VERSION_5;
	}
	private function isTimeBased():bool{
		return $this->version==UUIDVersion::VERSION_1;
	}
	private function isVersioned():bool{
		return isset($this->version);
	}
	private function isRandomBased():bool{
		return $this->version==UUIDVersion::VERSION_4;
	}
	public function getVersion():int{
		return $this->version->value;
	}
	protected function compile():string {
		if(!$this->isVersioned()){
			throw new UnVersionedException();
		}
		if($this->isNameBased()){
			$this->performHash();
			$this->setNode(substr($this->hash,20,12));
		}
		if($this->isTimeBased()){
			if($this->isTimestampTheSame()){
				$this->counter++;
			}else{
				$this->resetCounter();
			}
			$this->setTimestamp();
		}
		$this->resetComponents();
		$this->getTimeLow();
		$this->getTimeMid();
		$this->getTimeHighAndVersion();
		$this->getClockHighAndReserved();
		$this->getClockLow();
		$this->getNode();
		return sprintf(self::UUID_FORMAT_OUTPUT,...array_values($this->components));
	}
	abstract function generate():string;
}
