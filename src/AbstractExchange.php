<?php

namespace Kobens\Exchange;

use Kobens\Exchange\Book\{Book, BookInterface};
use Kobens\Exchange\Exception\Exception;

abstract class AbstractExchange implements ExchangeInterface
{
    /**
     * @var PairInterface[]
     */
    protected $pairs = [];

    /**
     * @param PairInterface[] $pairs
     */
    public function __construct(array $pairs)
    {
        $this->addPairs($pairs);
    }


    /**
     * Add currency pair to the exchange
     *
     * @param PairInterface[] $pairs
     * @throws Exception
     */
    private function addPairs(array $pairs)
    {
        foreach ($pairs as $pair) {
            if (!$pair instanceof PairInterface) {
                throw new Exception(\sprintf(
                    'Pair must be object of "%s"',
                    PairInterface::class
                ));
            }
            $this->pairs[$pair->getPairSymbol()] = $pair;
        }
    }

    public function getPair(string $key) : PairInterface
    {
        if (!isset($this->pairs[$key])) {
            throw new Exception(\sprintf(
                'Currency Pair "%s" not found on exchange',
                $key
            ));
        }
        return $this->pairs[$key];
    }


    public function getBook(string $pairKey) : BookInterface
    {
        return new Book($this, $pairKey);
    }
}