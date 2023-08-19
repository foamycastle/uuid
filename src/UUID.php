<?php

namespace FoamyCastle\UUID\Prototype;

use FoamyCastle\UUID\Enum\UUIDVersion;
use FoamyCastle\UUID\Exceptions\UnVersionedException;
use FoamyCastle\UUID\Exceptions\InvalidNodeValueException;
use FoamyCastle\UUID\Exceptions\InvalidNamespaceIDValueException;

abstract class UUID {

	/**
	 * Regex expression that validates UUID strings
	 */
	protected const UUID_FORMAT_VALID="/^([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})|([a-f0-9]{32})$/i";
	/**
	 * The format string used by sprintf() for UUID output
	 */
	private const NODE_FORMAT_VALID="/^[a-f0-9]{12}$/i";
	/**
	 * @var array contains the constituent elements of a UUID string after compilation
	 */
	protected const UUID_FORMAT_OUTPUT="%s-%s-%s-%s%s-%s";
	/**
	 * Regex expression that validates a user-supplied node ID
	 */
	protected array $components;
	/**
	 * @var string a 48-bit value used as an identifier in version 1 strings
	 */
	protected string $node;

	protected int $variant=4;
	/**
	 * @var \FoamyCastle\UUID\Enum\UUIDVersion the version of the UUID string
	 */
	protected UUIDVersion $version;
	/**
	 * @var string the most recently generated UUID
	 */
	protected string $uuid;
	abstract protected function getTimeLow():void;
	abstract protected function getTimeMid():void;
	abstract protected function getTimeHighAndVersion():void;
	abstract protected function getClockLow():void;
	abstract protected function getClockHighAndReserved():void;
	abstract function generate():string;
	function __toString():string{
		return $this->uuid ?? "";
	}
	protected function getARandomHexValue(int $nibblesLong, int $multiplex=0):string{
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
	protected function validateNodeValue(string $value):bool{
		return preg_match(self::NODE_FORMAT_VALID,$value)==1;
	}
	protected function getNode():string {
		$this->components['nodeValue']=$this->node;
		return $this->node ?? "";
	}
	private function resetComponents():void{
		$this->components=[];
	}
	public function getVersion():int{
		return $this->version->value;
	}
	protected function compile():string {
		$this->resetComponents();
		$this->getTimeLow();
		$this->getTimeMid();
		$this->getTimeHighAndVersion();
		$this->getClockHighAndReserved();
		$this->getClockLow();
		$this->getNode();
		return sprintf(self::UUID_FORMAT_OUTPUT,...array_values($this->components));
	}

    public static function validate(string $uuid):bool
    {
        return preg_match('/(?i)^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/',$uuid)==1;
    }
}
