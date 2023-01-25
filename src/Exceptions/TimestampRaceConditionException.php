<?php

namespace FoamyCastle\UUID\Exceptions;

class TimestampRaceConditionException extends \Exception {
	function __construct($nodeID, string $message = "", int $code = 0, ?\Throwable $previous = null) {
		parent::__construct(
			"A race condition for node ID '$nodeID' has occurred"
		);
	}
}