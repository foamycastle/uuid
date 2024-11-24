<?php

namespace Foamycastle\UUID\Builder;

use Foamycastle\UUID\UUIDBuilder;
use PHPUnit\Framework\TestCase;

class UUIDVersion4Test extends TestCase
{

    public function test__toString()
    {
        $uuid=UUIDBuilder::Version4();
        $this->expectsOutput();
        echo $uuid.PHP_EOL;
    }

}