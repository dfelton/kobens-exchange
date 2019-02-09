<?php

namespace Kobens\Exchange\Book;

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
     * @var \Kobens\Exchange\ExchangeInterface
     */
    protected $exchange;

    /**
     * @var \Kobens\Exchange\Pair\PairInterface
     */
    protected $pair;

    /**
     * @var \Zend\Cache\Storage\StorageInterface
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

    /**
     * @return \Kobens\Exchange\ExchangeInterface
     */
    public function getExchange() : \Kobens\Exchange\ExchangeInterface
    {
        return $this->exchange;
    }

    /**
     * Return the cache key for the current book
     *
     * @return string
     */
    protected function getBookCacheKey() : string
    {
        if (!$this->cacheKeyBook) {
            $this->cacheKeyBook = implode('::', [
                'kobens',
                $this->getExchange()->getCacheKey(),
                'market-book',
                $this->pair->getPairSymbol(),
            ]);
        }
        return $this->cacheKeyBook;
    }

    /**
     * @return string
     */
    protected function getLastTradeCacheKey() : string
    {
        if (!$this->cacheKeyLastTrade) {
            $this->cacheKeyLastTrade = implode('::', [
                'kobens',
                $this->getExchange()->getCacheKey(),
                $this->getBaseCurrency()->getCacheIdentifier(),
                $this->getQuoteCurrency()->getCacheIdentifier(),
                'last_trade'
            ]);
        }
        return $this->cacheKeyLastTrade;
    }

    /**
     * @return string
     */
    protected function getHeartbeatCacheKey() : string
    {
        if (!$this->cacheKeyHeartbeat) {
            $this->cacheKeyHeartbeat = implode('::', [
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
     * @throws \Kobens\Exchange\Exception\ClosedBookException
     * @return mixed
     */
    public function getBook()
    {
        $isAlive = $this->cache->test($this->getBookCacheKey());
        if (!$isAlive) {
            throw new \Kobens\Exchange\Exception\ClosedBookException('Market book is closed.');
        } elseif (time() - $isAlive >= $this->bookExpiration) {
            throw new \Kobens\Exchange\Exception\ClosedBookException('Market book has expired.');
        }
        return unserialize($this->cache->load($this->getBookCacheKey()));
    }

    /**
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair() : \Kobens\Exchange\Pair\PairInterface
    {
        return $this->pair;
    }

    /**
     * @return \Kobens\Currency\CurrencyInterface
     */
    public function getBaseCurrency() : \Kobens\Currency\CurrencyInterface
    {
        return $this->pair->getBaseCurrency();
    }

    /**
     * @return \Kobens\Currency\CurrencyInterface
     */
    public function getQuoteCurrency() : \Kobens\Currency\CurrencyInterface
    {
        return $this->pair->getQuoteCurrency();
    }
}