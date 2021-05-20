<?php

declare(strict_types=1);

namespace Kobens\Exchange\Book;

use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\{ExchangeInterface, PairInterface};
use Symfony\Component\Cache\Adapter\AdapterInterface;

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
     * @var AdapterInterface
     */
    private $cache;

    /**
     * @var PairInterface
     */
    private $pair;

    public function __construct(
        AdapterInterface $cacheAdapterInterface,
        ExchangeInterface $exchange,
        string $pairKey
    )
    {
        $this->cache = $cacheAdapterInterface;
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
        $item = $this->cache->getItem($this->util->getLastTradeCacheKey());
        return $item->isHit() ? $item->get() : null;
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
