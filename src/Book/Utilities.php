<?php

namespace Kobens\Exchange\Book;

use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\PairInterface;
use Kobens\Exchange\Exception\ClosedBookException;
use Kobens\Exchange\Exception\Exception;
use Zend\Cache\Storage\StorageInterface;
use Kobens\Core\Cache;

class Utilities
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    protected $pulseExpiration;

    /**
     * @var ExchangeInterface
     */
    protected $exchange;

    /**
     * @var PairInterface
     */
    protected $pair;

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @var string
     */
    protected $cacheKeyBook;

    /**
     * @var string
     */
    protected $cacheKeyHeartbeat;

    /**
     * @var string
     */
    protected $cacheKeyLastTrade;

    public function __construct(
        ExchangeInterface $exchange,
        string $pairKey,
        int $pulseExpiration = 6
    )
    {
        if (!$pulseExpiration > 0) {
            throw new Exception('Pulse expiration must be an integer greater than zero');
        }
        $this->exchange = $exchange;
        $this->pair = $exchange->getPair($pairKey);
        $this->cache = (new Cache())->getCache();
        $this->pulseExpiration = $pulseExpiration;
    }

    public function checkPulse() : void
    {
        $key = $this->getHeartbeatCacheKey();
        if (!$this->cache->hasItem($key)) {
            throw new ClosedBookException('Market book is closed.');
        }
        $meta = $this->cache->getMetadata($key);
        if ($meta === false) {
            throw new Exception('Unabled to fetch from cache');
        } elseif (\time() - $meta['mtime'] >= $this->pulseExpiration) {
            throw new ClosedBookException('Market book has expired.');
        }
    }

    /**
     * Return the cache key for the current book
     */
    public function getBookCacheKey() : string
    {
        if (!$this->cacheKeyBook) {
            $this->cacheKeyBook = \implode('_', [
                'kobens',
                $this->exchange->getCacheKey(),
                'market-book',
                $this->pair->getPairSymbol(),
            ]);
        }
        return $this->cacheKeyBook;
    }

    public function getLastTradeCacheKey() : string
    {
        if (!$this->cacheKeyLastTrade) {
            $this->cacheKeyLastTrade = \implode('_', [
                'kobens',
                $this->exchange->getCacheKey(),
                $this->pair->getBaseCurrency()->getCacheIdentifier(),
                $this->pair->getQuoteCurrency()->getCacheIdentifier(),
                'last_trade'
            ]);
        }
        return $this->cacheKeyLastTrade;
    }

    public function getHeartbeatCacheKey() : string
    {
        if (!$this->cacheKeyHeartbeat) {
            $this->cacheKeyHeartbeat = \implode('_', [
                'kobens',
                $this->exchange->getCacheKey(),
                $this->pair->getBaseCurrency()->getCacheIdentifier(),
                $this->pair->getQuoteCurrency()->getCacheIdentifier(),
                'heartbeat'
            ]);
        }
        return $this->cacheKeyHeartbeat;
    }

    /**
     * @throws Exception
     */
    public function getBook() : array
    {
        $this->checkPulse();
        $book = $this->cache->getItem($this->getBookCacheKey());
        if ($book === null) {
            throw new Exception('Unabled to fetch from cache');
        }
        $book = json_decode($book, true);
        return $book;
    }

}

