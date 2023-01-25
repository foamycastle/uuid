<?php

namespace FoamyCastle\UUID\Exceptions;

class InvalidNodeValueException extends \Exception {
	function __construct(string $message = "", int $code = 0, ?Throwable $previous = null) {
		parent::__construct("The given node value is not valid. 12 hex chars expected");
	}
}