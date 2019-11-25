<?php

namespace Kobens\Exchange\Book\Keeper;

use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Book\BookTraits;
use Kobens\Exchange\Book\Utilities;
use Kobens\Exchange\Book\Trade\TradeInterface;
use Zend\Cache\Storage\StorageInterface;

abstract class AbstractKeeper implements KeeperInterface
{
    use BookTraits;

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @var Utilities
     */
    protected $util;

    /**
     * @var PairInterface
     */
    protected $pair;

    public function __construct(
        StorageInterface $storageInterface,
        ExchangeInterface $exchangeInterface,
        string $pairKey
    ) {
        $this->cache = $storageInterface;
        $this->exchange = $exchangeInterface;
        $this->pair = $exchangeInterface->getPair($pairKey);
        $this->util = new Utilities($exchangeInterface, $pairKey);
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
        $this->validateSide($makerSide);
        $book = $this->util->getBook();
        if (\floatval($remaining) === \floatval(0)) {
            unset($book[$makerSide][$quote]);
        } else {
            $book[$makerSide][$quote] = $remaining;
        }
        $book = \json_encode($book);
        $this->cache->setItem(
            $this->util->getBookCacheKey(),
            $book
        );
    }

    protected function populateBook(array $book) : void
    {
        foreach (['bid', 'ask'] as $key) {
            if (!isset($book[$key]) || !is_array($book[$key])) {
                // @todo throw exception
            }
        }
        $book = \json_encode($book);
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
