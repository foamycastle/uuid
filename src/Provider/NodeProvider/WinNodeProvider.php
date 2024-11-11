<?php

namespace Foamycastle\UUID\Provider\NodeProvider;

use Foamycastle\UUID\Provider\NodeProvider;
use Foamycastle\UUID\Provider\ProviderKey;

class WinNodeProvider extends NodeProvider
{
    public function __construct()
    {
        parent::__construct(ProviderKey::NODE_WIN);
    }

    protected function shellCommand(): string
    {
        return 'ipconfig /all 2>&1';
    }

}