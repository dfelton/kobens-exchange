<?php

namespace Kobens\Exchange\Book\Keeper;

use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\Book\Utilities;
use Kobens\Exchange\ExchangeInterface;
use Zend\Cache\Storage\StorageInterface;

abstract class AbstractKeeper implements KeeperInterface
{
    /**
     * @var Utilities
     */
    protected $util;

    /**
     * @var StorageInterface
     */
    protected $cache;

    public function __construct(
        ExchangeInterface $exchange,
        string $pairKey
    ) {
        $this->cache = $exchange->getCache();
        $this->exchange = $exchange;
        $this->util = new Utilities($exchange, $pairKey);
    }

    protected function setPulse() : bool
    {
        return $this->cache->setItem(
            $this->util->getHeartbeatCacheKey(),
            (string) \microtime(true)
        );
    }

    /**
     * Update the book
     */
    protected function updateBook(string $makerSide, string $quote, string $remaining) : void
    {
        $book = $this->getBook();
        if (\floatval($remaining) === \floatval(0)) {
            unset($book[$makerSide][$quote]);
        } else {
            $book[$makerSide][$quote] = $remaining;
        }
        $this->cache->setItem(
            $this->getBookCacheKey(),
            $book
        );
    }

    protected function populateBook(array $book) : void
    {
        $this->cache->setItem(
            $this->util->getBookCacheKey(),
            $book
        );
    }

    protected function setLastTrade(TradeInterface $trade) : void
    {
        $this->cache->setItem(
            $this->util->getLastTradeCacheKey(),
            $trade
        );
    }
}