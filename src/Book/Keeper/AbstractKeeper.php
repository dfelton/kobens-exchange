<?php

namespace Kobens\Exchange\Book\Keeper;

use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Pair\PairInterface;
use Zend\Cache\Storage\StorageInterface;

abstract class AbstractKeeper implements KeeperInterface
{
    use \Kobens\Exchange\Book\BookTraits;

    /**
     * @var StorageInterface
     */
    protected $cache;

    public function __construct(
        ExchangeInterface $exchangeInterface,
        PairInterface $pairInterface
    ) {
        $this->cache = $exchangeInterface->getCache();
        $this->exchange = $exchangeInterface;
        $this->pair = $pairInterface;
    }


    protected function setPulse() : bool
    {
        return $this->cache->setItem(
            $this->getHeartbeatCacheKey(),
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
            $this->getBookCacheKey(),
            $book
        );
    }

    protected function setLastTrade(TradeInterface $trade) : void
    {
        $this->cache->setItem(
            $this->getLastTradeCacheKey(),
            $trade
        );
    }
}