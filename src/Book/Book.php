<?php
namespace Kobens\Exchange\Book;

use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\{ExchangeInterface, PairInterface};
use Zend\Cache\Storage\StorageInterface;

class Book implements BookInterface
{
    use BookTraits;

    /**
     * @var ExchangeInterface
     */
    protected $exchange;

    /**
     * @var Utilities
     */
    protected $util;

    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @var PairInterface
     */
    protected $pair;

    public function __construct(
        ExchangeInterface $exchange,
        string $pairKey
    )
    {
        $this->cache = $exchange->getCache();
        $this->exchange = $exchange;
        $this->pair = $exchange->getPair($pairKey);
        $this->util = new Utilities($exchange, $pairKey);
    }

    public function getRawBookData(): array
    {
        return $this->util->getBook();
    }

    public function getRemaining(string $makerSide, string $rate): string
    {
        $this->validateSide($makerSide);
        $book = $this->getRawBookData();
        return isset($book[$makerSide][$rate]) ? $book[$makerSide][$rate] : '0';
    }

    public function getLastTrade(): ?TradeInterface
    {
        $this->util->checkPulse();
        $key = $this->util->getLastTradeCacheKey();
        return $this->cache->getItem($key);
    }

    public function getAskPrice(): string
    {
        $orders = $this->getRawBookData()['ask'];
        \ksort($orders);
        $price = \reset($orders);
        if ($price === false) {
            // @todo throw exception
        }
        return $price;
    }

    public function getSpread(): string
    {
        return \bcsub(
            $this->getAskPrice(),
            $this->getBidPrice(),
            $this->pair->getQuoteCurrency()->getScale()
        );
    }

    public function getBidPrice(): string
    {
        $orders = $this->getRawBookData()['bid'];
        \ksort($orders);
        $price = \end($orders);
        if ($price === false) {
            // @todo throw exception
        }
        return $price;
    }

}

