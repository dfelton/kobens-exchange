<?php

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
        $this->addPairs($pairs);
    }

    /**
     * Add currency pair to the exchange
     *
     * @param PairInterface[] $pairs
     * @throws Exception
     */
    final private function addPairs(array $pairs)
    {
        foreach ($pairs as $pair) {
            if (!$pair instanceof PairInterface) {
                throw new Exception(\sprintf(
                    'Pair must be object of "%s"',
                    PairInterface::class
                ));
            }
            if (isset($this->pairs[$pair->symbol])) {
                throw new \LogicException("Pair \"{$pair->symbol}\" already exists.");
            }
            $this->pairs[$pair->symbol] = $pair;
        }
    }

    final public function getPair(string $key) : PairInterface
    {
        if (!isset($this->pairs[$key])) {
            throw new Exception(\sprintf(
                'Currency Pair "%s" not found on exchange',
                $key
            ));
        }
        return $this->pairs[$key];
    }


    final public function getBook(string $pairKey) : BookInterface
    {
        return new Book($this, $pairKey);
    }
}