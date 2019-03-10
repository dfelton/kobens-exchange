<?php

namespace Kobens\Exchange\Book\Keeper;

use Kobens\Core\Cache;
use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\Book\{BookTraits, Utilities};
use Kobens\Exchange\{ExchangeInterface, PairInterface};
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
        ExchangeInterface $exchange,
        string $pairKey
    ) {
        $this->cache = (new Cache())->getCache();
        $this->exchange = $exchange;
        $this->pair = $exchange->getPair($pairKey);
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