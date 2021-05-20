<?php

declare(strict_types=1);

namespace Kobens\Exchange\Book\Keeper;

use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Book\BookTraits;
use Kobens\Exchange\Book\Utilities;
use Kobens\Exchange\Book\Trade\TradeInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

abstract class AbstractKeeper implements KeeperInterface
{
    use BookTraits;

    /**
     * @var AdapterInterface
     */
    protected $cache;

    /**
     * @var Utilities
     */
    protected $util;

    /**
     * @var \Kobens\Exchange\PairInterface
     */
    protected $pair;

    public function __construct(
        AdapterInterface $cacheAdapter,
        ExchangeInterface $exchangeInterface,
        string $pairKey
    ) {
        $this->cache = $cacheAdapter;
        $this->exchange = $exchangeInterface;
        $this->pair = $exchangeInterface->getPair($pairKey);
        $this->util = new Utilities($exchangeInterface, $pairKey);
    }

    /**
     * Updates the pulse of the book. Returns true on success false on error.
     *
     * @return bool
     */
    protected function setPulse(): bool
    {
        return $this->cache->save(
            $this->cache->getItem($this->util->getHeartbeatCacheKey())->set((string) \microtime(true))
        );
    }

    /**
     * Update the book
     */
    protected function updateBook(string $makerSide, string $quote, string $remaining): void
    {
        $this->validateSide($makerSide);
        $book = $this->util->getBook();
        if (\floatval($remaining) === \floatval(0)) {
            unset($book[$makerSide][$quote]);
        } else {
            $book[$makerSide][$quote] = $remaining;
        }
        $book = \json_encode($book);
        $this->cache->save(
            $this->cache->getItem($this->util->getBookCacheKey())->set($book)
        );
    }

    protected function populateBook(array $book): void
    {
        if (!is_array($book['bid'] ?? null) || !is_array($book['ask'] ?? null)) {
            throw new \Exception('Book is missing bid or ask side.');
        }
        $this->cache->save(
            $this->cache->getItem($this->util->getBookCacheKey())->set(\json_encode($book))
        );
    }

    protected function setLastTrade(TradeInterface $trade): void
    {
        $this->cache->save(
            $this->cache->getItem($this->util->getLastTradeCacheKey())->set($trade)
        );
    }
}
