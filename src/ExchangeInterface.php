<?php

namespace Kobens\Exchange;

interface ExchangeInterface
{
    /**
     * @return string
     */
    public function getCacheKey() : string;

    /**
     * @param string $key
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair($key) : \Kobens\Exchange\Pair\PairInterface;

     /**
      * @return \Zend\Cache\Storage\StorageInterface
      */
     public function getCache() : \Zend\Cache\Storage\StorageInterface;
}
