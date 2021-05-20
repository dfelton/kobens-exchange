<?php

declare(strict_types=1);

namespace Kobens\Exchange\Book;

use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\PairInterface;
use Kobens\Exchange\Exception\ClosedBookException;
use Kobens\Exchange\Exception\Exception;
use Symfony\Component\Cache\Adapter\AdapterInterface;

final class Utilities
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    private $pulseExpiration;

    /**
     * @var ExchangeInterface
     */
    private $exchange;

    /**
     * @var PairInterface
     */
    private $pair;

    /**
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var string
     */
    private $cacheKeyBook;

    /**
     * @var string
     */
    private $cacheKeyHeartbeat;

    /**
     * @var string
     */
    private $cacheKeyLastTrade;

    public function __construct(
        AdapterInterface $cacheAdapterInterface,
        ExchangeInterface $exchange,
        string $pairKey,
        int $pulseExpiration = 6
    ) {
        if (!$pulseExpiration > 0) {
            throw new Exception('Pulse expiration must be greater than zero');
        }
        $this->exchange = $exchange;
        $this->pair = $exchange->getPair($pairKey);
        $this->cache = $cacheAdapterInterface;
        $this->pulseExpiration = $pulseExpiration;
    }

    public function checkPulse(): void
    {
        $item = $this->cache->getItem($this->getHeartbeatCacheKey());
        if (!$item->isHit()) {
            throw new ClosedBookException('Market book is closed.');
        }
        $meta = $item->getMetadata();
        if ($meta === false) {
            throw new Exception('Unabled to fetch from cache');
        } elseif (\time() - $meta['mtime'] >= $this->pulseExpiration) {
            throw new ClosedBookException('Market book has expired.');
        }
    }

    /**
     * Return the cache key for the current book
     */
    public function getBookCacheKey(): string
    {
        if (!$this->cacheKeyBook) {
            $this->cacheKeyBook = \implode('_', [
                'kobens',
                $this->exchange->getCacheKey(),
                'market-book',
                $this->pair->symbol,
            ]);
        }
        return $this->cacheKeyBook;
    }

    public function getLastTradeCacheKey(): string
    {
        if (!$this->cacheKeyLastTrade) {
            $this->cacheKeyLastTrade = \implode('_', [
                'kobens',
                $this->exchange->getCacheKey(),
                $this->pair->base->symbol,
                $this->pair->quote->symbol,
                'last_trade'
            ]);
        }
        return $this->cacheKeyLastTrade;
    }

    public function getHeartbeatCacheKey(): string
    {
        if (!$this->cacheKeyHeartbeat) {
            $this->cacheKeyHeartbeat = \implode('_', [
                'kobens',
                $this->exchange->getCacheKey(),
                $this->pair->base->symbol,
                $this->pair->quote->symbol,
                'heartbeat'
            ]);
        }
        return $this->cacheKeyHeartbeat;
    }

    /**
     * @throws Exception
     */
    public function getBook(): array
    {
        $this->checkPulse();
        $book = $this->cache->getItem($this->getBookCacheKey());
        if ($book->isHit() === false) {
            throw new Exception('Unabled to fetch from cache');
        }
        return \json_decode($book->get(), true);
    }
}
