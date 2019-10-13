<?php

namespace Kobens\Exchange\Book;

use Kobens\Core\Cache;
use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\{ExchangeInterface, PairInterface};
use Zend\Cache\Storage\StorageInterface;

final class Book implements BookInterface
{
    use BookTraits;

    /**
     * @var ExchangeInterface
     */
    private $exchange;

    /**
     * @var Utilities
     */
    private $util;

    /**
     * @var StorageInterface
     */
    private $cache;

    /**
     * @var PairInterface
     */
    private $pair;

    public function __construct(
        ExchangeInterface $exchange,
        string $pairKey
    )
    {
        $this->cache = Cache::getInstance();
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
        $price = \array_keys($orders);
        $price = \reset($price);
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
            $this->pair->quote->scale
        );
    }

    public function getBidPrice(): string
    {
        $orders = $this->getRawBookData()['bid'];
        \ksort($orders);
        $price = \array_keys($orders);
        $price = \end($price);
        if ($price === false) {
            // @todo throw exception
        }
        return $price;
    }

    public function getSymbol() : string
    {
        return $this->pair->symbol;
    }

}


