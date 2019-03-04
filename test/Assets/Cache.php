<?php

namespace KobensTest\Exchange\Assets;

use Zend\Cache\Storage\Adapter\Filesystem;

class Cache extends Filesystem
{
    public function __construct()
    {
        parent::__construct(['cache_dir' => '/tmp/kobens/kobens-exchange-test']);
    }
}

