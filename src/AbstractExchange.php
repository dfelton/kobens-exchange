<?php

namespace Kobens\Exchange;

abstract class AbstractExchange
{
    /**
     * @var \Zend\Cache\Storage\StorageInterface
     */
    protected $cache;

    /**
     * @var \Kobens\Exchange\Pair\PairInterface[]
     */
    protected $pairs = [];

    /**
     * @param \Zend\Cache\Storage\StorageInterface $cacheInterface
     * @param \Kobens\Exchange\Pair\PairInterface[] $pairs
     */
    public function __construct(
        \Zend\Cache\Storage\StorageInterface $cacheInterface,
        array $pairs = []
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
        if (!is_array($pairs)) {
            $pairs = [$pairs];
        }
        foreach ($pairs as $pair) {
            if (!$pair instanceof \Kobens\Exchange\Pair\PairInterface) {
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
    public function getCache() : \Zend\Cache\Storage\StorageInterface
    {
        return $this->cache;
    }

    /**
     * @param string $key
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair(string $key) : \Kobens\Exchange\Pair\PairInterface
    {
        if (!isset($this->pairs[$key])) {
            throw new \Exception(\sprintf('Currency Pair "%s" not found on exchange', $key));
        }
        return $this->pairs[$key];
    }
}