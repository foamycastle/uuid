<?php

namespace FoamyCastle\UUID\Exceptions;

class InvalidNamespaceIDValueException extends \Exception {
	function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null) {
		parent::__construct("The given namespace ID is not valid");
	}
}