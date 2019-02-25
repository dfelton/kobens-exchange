<?php

namespace Kobens\Exchange\Book;

use Kobens\Currency\CurrencyInterface;
use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Pair\PairInterface;
use Kobens\Exchange\Exception\ClosedBookException;
use Zend\Cache\Storage\StorageInterface;

/**
 * @todo Reconsider this whole trait.... @see https://codereview.stackexchange.com/a/74195/193755
 */
trait BookTraits
{
    /**
     * Time (in seconds) to consider a book closed if
     * no updates have occurred between now and last update.
     *
     * @var integer
     */
    protected $bookExpiration = 5;

    /**
     * The exchange market's order book
     *
     * @var array
     */
    protected $book;

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

    public function getExchange() : ExchangeInterface
    {
        return $this->exchange;
    }

    /**
     * Return the cache key for the current book
     */
    protected function getBookCacheKey() : string
    {
        if (!$this->cacheKeyBook) {
            $this->cacheKeyBook = \implode('::', [
                'kobens',
                $this->getExchange()->getCacheKey(),
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
                $this->getExchange()->getCacheKey(),
                $this->getBaseCurrency()->getCacheIdentifier(),
                $this->getQuoteCurrency()->getCacheIdentifier(),
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
                $this->getExchange()->getCacheKey(),
                $this->getBaseCurrency()->getCacheIdentifier(),
                $this->getQuoteCurrency()->getCacheIdentifier(),
                'heartbeat'
            ]);
        }
        return $this->cacheKeyHeartbeat;
    }

    /**
     * Return the market's order book.
     *
     * @throws ClosedBookException
     * @return mixed
     */
    public function getBook()
    {
        if (!$this->cache->hasItem($this->getBookCacheKey())) {
            throw new ClosedBookException('Market book is closed.');
        }
        $meta = $this->cache->getMetadata($this->getBookCacheKey());
        if (\time() - $meta['mtime'] >= $this->bookExpiration) {
            throw new ClosedBookException('Market book has expired.');
        }
        return $this->cache->getItem($this->getBookCacheKey());
    }

    public function getPair() : PairInterface
    {
        return $this->pair;
    }

    public function getBaseCurrency() : CurrencyInterface
    {
        return $this->pair->getBaseCurrency();
    }

    public function getQuoteCurrency() : CurrencyInterface
    {
        return $this->pair->getQuoteCurrency();
    }

}

