<?php

namespace Foamycastle\UUID\Provider\TimeProvider;

use PHPUnit\Framework\TestCase;

class UnixTimeTest extends TestCase
{

    public function testRefreshData()
    {
        $time=new UnixTime();
        $this->expectsOutput();
        echo $time->getData().PHP_EOL;
    }

}
