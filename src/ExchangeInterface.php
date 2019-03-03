<?php

namespace Kobens\Exchange;

use Zend\Cache\Storage\StorageInterface;

interface ExchangeInterface
{
    public function getCacheKey() : string;

    public function getPair(string $key) : PairInterface;

    public function getCache() : StorageInterface;
}
