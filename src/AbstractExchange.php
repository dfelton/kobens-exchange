<?php

namespace Kobens\Exchange;

use Zend\Cache\Storage\StorageInterface;
use Kobens\Exchange\Pair\PairInterface;

abstract class AbstractExchange
{
    /**
     * @var StorageInterface
     */
    protected $cache;

    /**
     * @var PairInterface[]
     */
    protected $pairs = [];

    /**
     * @param StorageInterface $cacheInterface
     * @param PairInterface[] $pairs
     */
    public function __construct(
        StorageInterface $cacheInterface,
        array $pairs
    ) {
        $this->cache = $cacheInterface;
        $this->addPairs($pairs);
    }

    /**
     * Add currency pair to the exchange
     *
     * @param \Kobens\Exchange\Pair\PairInterface[] $pairs
     * @throws \Exception
     */
    protected function addPairs(array $pairs)
    {
        foreach ($pairs as $pair) {
            if (!$pair instanceof PairInterface) {
                throw new \Exception('Invalid Pair Interface');
            }
            $base = $pair->getBaseCurrency()->getPairIdentity();
            $quote = $pair->getQuoteCurrency()->getPairIdentity();
            $this->pairs[$base.'/'.$quote] = $pair;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Exchange\ExchangeInterface::getCache()
     */
    public function getCache() : StorageInterface
    {
        return $this->cache;
    }

    /**
     * @param string $key
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair(string $key) : PairInterface
    {
        if (!isset($this->pairs[$key])) {
            throw new \Exception(\sprintf(
                'Currency Pair "%s" not found on exchange',
                $key
            ));
        }
        return $this->pairs[$key];
    }
}