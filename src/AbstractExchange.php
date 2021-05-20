<?php

declare(strict_types=1);

namespace Kobens\Exchange;

use Kobens\Exchange\Book\{Book, BookInterface};
use Kobens\Exchange\Exception\Exception;

abstract class AbstractExchange implements ExchangeInterface
{
    /**
     * @var PairInterface[]
     */
    private $pairs = [];

    /**
     * @param PairInterface[] $pairs
     */
    protected function __construct(array $pairs)
    {
        foreach ($pairs as $pair) {
            $this->addPair($pair);
        }
    }

    /**
     * Add currency pair to the exchange
     *
     * @throws \LogicException
     */
    final private function addPair(PairInterface $pair): void
    {
        if (isset($this->pairs[$pair->symbol])) {
            throw new \LogicException("Pair \"{$pair->symbol}\" already exists.");
        }
        $this->pairs[$pair->symbol] = $pair;
    }

    final public function getPair(string $key): PairInterface
    {
        if (!isset($this->pairs[$key])) {
            throw new Exception("Currency Pair \"$key\" not found on exchange");
        }
        return $this->pairs[$key];
    }

    final public function getBook(string $pairKey): BookInterface
    {
        return new Book($this, $pairKey);
    }
}
