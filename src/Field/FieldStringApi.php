<?php

namespace Foamycastle\UUID\Field;

use Foamycastle\UUID\FieldApi;

interface FieldStringApi
{

    /**
     * Specifies the location in the string provided by provider to begin extracting data
     * @param int $start
     * @return self
     */
    function startAt(int $start):FieldStringApi&FieldApi;
}