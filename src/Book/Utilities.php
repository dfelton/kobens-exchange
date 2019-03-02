<?php

namespace Kobens\Exchange\Book;

use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Pair\PairInterface;
use Kobens\Exchange\Exception\ClosedBookException;
use Kobens\Exchange\Exception\Exception;
use Zend\Cache\Storage\StorageInterface;

class Utilities
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    protected $bookExpiration = 5;

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
        string $pairKey
    )
    {
        $this->exchange = $exchange;
        $this->pair = $exchange->getPair($pairKey);
        $this->cache = $exchange->getCache();
    }

    /**
     * Return the cache key for the current book
     */
    protected function getBookCacheKey() : string
    {
        if (!$this->cacheKeyBook) {
            $this->cacheKeyBook = \implode('::', [
                'kobens',
                $this->exchange->getCacheKey(),
                'market-book',
                $this->pair->getPairSymbol(),
            ]);
        }
        return $this->cacheKeyBook;
    }

    protected function getLastTradeCacheKey() : string
    {
        if (!$this->cacheKeyLastTrade) {
            $this->cacheKeyLastTrade = \implode('::', [
                'kobens',
                $this->exchange->getCacheKey(),
                $this->pair->getBaseCurrency()->getCacheIdentifier(),
                $this->pair->getQuoteCurrency()->getCacheIdentifier(),
                'last_trade'
            ]);
        }
        return $this->cacheKeyLastTrade;
    }

    protected function getHeartbeatCacheKey() : string
    {
        if (!$this->cacheKeyHeartbeat) {
            $this->cacheKeyHeartbeat = \implode('::', [
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
     * @throws ClosedBookException
     * @throws Exception
     */
    public function getBook() : array
    {
        $key = $this->getBookCacheKey();
        if (!$this->cache->hasItem($key)) {
            throw new ClosedBookException('Market book is closed.');
        }
        $meta = $this->cache->getMetadata($key);
        if ($meta === false) {
            throw new Exception('Unabled to fetch from cache');
        } elseif (\time() - $meta['mtime'] >= $this->bookExpiration) {
            throw new ClosedBookException('Market book has expired.');
        }
        $book = $this->cache->getItem($key);
        if ($book === null) {
            throw new Exception('Unabled to fetch from cache');
        }
        return $this->cache->getItem($key);
    }

}

