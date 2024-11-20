<?php

namespace Foamycastle\UUID\Provider;

use Foamycastle\UUID\Provider;
use Foamycastle\UUID\ProviderApi;

class HashProvider extends Provider implements ProvidesBinary, ProvidesHex
{
    public function __construct(
        private readonly string $namespace,
        private readonly string $key,
        private readonly int $version
    )
    {
        $this->data=match ($this->version){
            3=>md5($this->namespace.$this->key,true),
            5=>sha1($this->namespace.$this->key,true)
        };
    }

    function refreshData(): ProviderApi
    {
        return $this;
    }

    function getBinary(): string
    {
        return substr($this->data,0,16);
    }

    function reset(): ProviderApi
    {
        return $this;
    }

    function toHex(): string
    {
        return substr(bin2hex($this->data),0,32);
    }

}