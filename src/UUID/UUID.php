<?php

namespace FoamyCastle\UUID;
use FoamyCastle\UUID\UUIDVersion;
use FoamyCastle\UUID\UUIDVariant;

use FoamyCastle\UUID\Exceptions\InvalidNodeValueException;
use FoamyCastle\UUID\Exceptions\InvalidNamespaceIDValueException;
use FoamyCastle\UUID\Exceptions\TimestampRaceConditionException;

class UUID {
	/**
	 * The number of 100ns periods between 15-Oct-1582 and 1-Jan-1970
	 */
	private const GREGORIAN_TO_UNIX=122192928000000000;
	/**
	 * Regex expression that validates UUID strings
	 */
	private const UUID_FORMAT_VALID="/^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})|([a-f0-9]{32})$/i";
	/**
	 * Regex expression that validates a user-supplied node ID
	 */
	private const NODE_FORMAT_VALID="/^[a-f0-9]{12}$/i";
	/**
	 * The format string used by sprintf() for UUID output
	 */
	private const UUID_FORMAT_OUTPUT="%s-%s-%s-%s%s-%s";
	/**
	 * The maximum number of UUID string that can be generated in a given timestamp window
	 */
	private const COUNTER_MAX=0x1FFF;
	/**
	 * @var array contains the constituent elements of a UUID string after compilation
	 */
	private array $components;
	/**
	 * @var int The current timestamp measured in 1µs periods
	 */
	private int $timestamp;
	/**
	 * @var string namespace for version 3 and version 5 UUID strings
	 */
	private string $namespace;
	/**
	 * @var string the UUID string that is used for version 3 and version 5 generation
	 */
	private string $namespaceID;
	/**
	 * @var string a 48-bit value used as an identifier in version 1 strings
	 */
	private string $node;
	/**
	 * @var array|string[] hash algorithms used in version 3 and version 5
	 */
	private array $hashAlgorithms=[
		3=>'MD5',
		5=>'sha1'
	];
	/**
	 * @var string|mixed the chosen hash algorithm for the generator
	 */
	private string $hashAlgorithm;
	/**
	 * @var string a hash string generated for version 3 and version 5
	 */
	private string $hash;
	/**
	 * @var int counter used in the version 1 strings
	 */
	private int $counter;
	/**
	 * @var string the most recently generated UUID
	 */
	private string $uuid;
	/**
	 * @var bool flag used to prevent some methods from being used without
	 *           using the static constructors
	 */
	private bool $isInit=false;
	/**
	 * @var \FoamyCastle\UUID\UUIDVersion the version of the UUID string
	 */
	private UUIDVersion $version;
	/**
	 * @var \FoamyCastle\UUID\UUIDVariant the reserved bits indicating a schema variant
	 */
	private UUIDVariant $variant;
	private function __construct(UUIDVersion $version=UUIDVersion::VERSION_1,?string $node=null,?string $namespace=null,?string $namespaceID=null){

		//set the RFC4122 version
		$this->version=$version;

		//currently, the only supported variant is the one outlined in RFC4122
		$this->variant=UUIDVariant::VARIANT_RFC4122;

		//version 3 and 5
		if($this->isNameBased()){
			$this->setNamespace($namespace ?? $this->getNamespace());
			$this->setNamespaceID($namespaceID ?? $this->getNamespaceID());
			$this->hashAlgorithm=$this->hashAlgorithms[$this->version->value];
			return;
		}
		//version 1
		if($this->isTimeBased()){
			$this->setTimestamp();
			$this->resetCounter();
		}
		//Set the node value to either the supplied value or a random one
		$this->setNode($node);
	}
	function __toString():string{
		return $this->uuid ?? "";
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
	public function setNode(?string $value=null):UUID {
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
	private function getARandomHexValue(int $nibblesLong, int $multiplex=0):string{
		$minValue="1".str_pad("",$nibblesLong-1,"0");
		$maxValue=str_pad("",$nibblesLong,"F");
		return sprintf("%x",mt_rand(hexdec($minValue),hexdec($maxValue)) | $multiplex);
	}
	public function getVersion():int{
		return $this->version->value;
	}
	public function getNamespaceID():string{
		return $this->namespaceID ?? $this->namespaceID=str_repeat($this->getARandomHexValue(8),4);
	}
	public function getNamespace():string{
		return $this->namespace ?? $this->namespace=base64_encode(random_bytes(6));
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
				(($this->getCounter() >> 8) & 0x1F) | (($this->variant->value << 5) & 0xe0)
			);
			return;
		}
		if ($this->isNameBased()) {
			$this->components['clockHigh'] = sprintf(
				"%x%s",
				(($this->variant->value << 1) & 0xe) | (hexdec(substr($this->hash, 16, 1)) & 0x1),
				substr($this->hash, 17, 1)
			);
			return;
		}
		$this->components['clockHigh'] = $this->getARandomHexValue(2);
	}
	public function getNode():string {
		$this->components['nodeValue'] = $this->node;
		return $this->node ?? "";
	}
	private function isTimeBased():bool{
		return $this->version==UUIDVersion::VERSION_1;
	}
	private function isNameBased():bool{
		return $this->version==UUIDVersion::VERSION_3 || $this->version==UUIDVersion::VERSION_5;
	}
	private function isRandomBased():bool{
		return $this->version==UUIDVersion::VERSION_4;
	}
	private function isTimestampTheSame():bool{
		return $this->timestamp==$this->getTimestamp();
	}
	private function resetCounter():void{
		$this->counter=0;
	}
	private function resetComponents():void{
		$this->components=[];
	}
	private function validateNamespaceID(string $value):bool{
		return preg_match(self::UUID_FORMAT_VALID,$value)==1;
	}
	private function validateNodeValue(string $value):bool{
		return preg_match(self::NODE_FORMAT_VALID,$value)==1;
	}
	private function performHash():void {
		$this->hash=openssl_digest(
			$this->getNamespaceID() . $this->getNamespace(),
			$this->hashAlgorithm
		);
	}
	private function compile():void{
		if($this->isNameBased()){
			$this->performHash();
			$this->setNode(substr($this->hash,20,12));
		}
		if($this->isTimeBased()){
			if ($this->counter>self::COUNTER_MAX&&$this->isTimestampTheSame()){
				/*unlikely that this would ever happen. to many numbers have been
				  generated within the timestamp window */
				throw new TimestampRaceConditionException($this->node);
			}
			if ($this->counter>self::COUNTER_MAX){
				$this->resetCounter();
			}
			$this->setTimestamp();
		}
		$this->getTimeLow();
		$this->getTimeMid();
		$this->getTimeHighAndVersion();
		$this->getClockHighAndReserved();
		$this->getClockLow();
		$this->getNode();
		$this->uuid=sprintf(self::UUID_FORMAT_OUTPUT,...array_values($this->components));
		$this->resetComponents();
		if($this->isTimeBased()) $this->counter++;
	}
	public function generate():UUID{
		if(!$this->isInit){
			return $this;
		}
		$this->compile();
		return $this;
	}
	public static function TimeBased(?string $node=null):UUID{
		$newObj=new self(
			UUIDVersion::VERSION_1,
			$node
		);
		$newObj->isInit=true;
		return $newObj;
	}
	public static function NameBased_MD5(string $namespace,?string $namespaceID=null):UUID{
		return new self(
			UUIDVersion::VERSION_3,
			null,
			$namespace,
			$namespaceID
		);
	}
	public static function NameBased_SHA1(string $namespace,?string $namespaceID=null):UUID{
		return new self(
			UUIDVersion::VERSION_5,
			null,
			$namespace,
			$namespaceID
		);
	}
	public static function RandomBased():UUID{
		return new self(UUIDVersion::VERSION_4);
	}
}
