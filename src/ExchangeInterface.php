<?php

namespace Kobens\Exchange;

use Zend\Cache\Storage\StorageInterface;
use Kobens\Exchange\Book\BookInterface;
use Kobens\Exchange\Book\Keeper\KeeperInterface;

interface ExchangeInterface
{
    public function getCacheKey() : string;

    public function getPair(string $key) : PairInterface;

    public function getCache() : StorageInterface;

    public function getBookKeeper(string $key) : KeeperInterface;

    public function getBook(string $key) : BookInterface;

}
