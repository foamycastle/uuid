<?php

namespace FoamyCastle\UUID\Exceptions;

class UnVersionedException extends \Exception {
	public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null) {
		parent::__construct("Please use one of the instantiation classes");
	}
}