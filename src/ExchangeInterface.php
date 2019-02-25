<?php

namespace Kobens\Exchange;

interface ExchangeInterface
{
    public function getCacheKey() : string;

    public function getPair(string $key) : \Kobens\Exchange\Pair\PairInterface;

    public function getCache() : \Zend\Cache\Storage\StorageInterface;
}
