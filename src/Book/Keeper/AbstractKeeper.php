<?php

namespace Kobens\Exchange\Book\Keeper;

abstract class AbstractKeeper implements KeeperInterface
{
    use \Kobens\Exchange\Book\BookTraits;

    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     * @param \Kobens\Exchange\Pair\PairInterface $pairInterface
     * @param \Kobens\Exchange\ExchangeInterface $exchangeInterface
     */
    public function __construct(
        \Kobens\Exchange\ExchangeInterface $exchangeInterface,
        \Kobens\Exchange\Pair\PairInterface $pairInterface
    ) {
        $this->cache = $exchangeInterface->getCache();
        $this->exchange = $exchangeInterface;
        $this->pair = $pairInterface;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Exchange\Book\Keeper\KeeperInterface::openBook()
     */
    abstract function openBook();

    /**
     * @return bool
     */
    protected function setPulse()
    {
        return $this->cache->setItem(
            $this->getHeartbeatCacheKey(),
            (string) microtime(true)
        );
    }

    /**
     * Update the book
     *
     * @param string $makerSide
     * @param string $quote
     * @param string $remaining
     */
    protected function updateBook(string $makerSide, string $quote, string $remaining)
    {
        $book = $this->getBook();
        if (floatval($remaining) === floatval(0)) {
            unset($book[$makerSide][(string) $quote]);
        } else {
            $book[$makerSide][(string) $quote] = $remaining;
        }
        $this->cache->setItem(
            $this->getBookCacheKey(),
            $book
        );
    }

    /**
     * @param array $book
     */
    protected function populateBook(array $book)
    {
        $this->cache->setItem(
            $this->getBookCacheKey(),
            $book
        );
    }

    /**
     * @param \Kobens\Exchange\Book\Trade\TradeInterface $trade
     */
    protected function setLastTrade(\Kobens\Exchange\Book\Trade\TradeInterface $trade)
    {
        $this->cache->setItem(
            $this->getLastTradeCacheKey(),
            $trade
        );
    }
}