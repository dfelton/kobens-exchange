<?php

namespace Kobens\Exchange;

/**
 * TODO: Replace magento's cache interface with chosen caching interface.
 */
abstract class AbstractExchange
{
    /**
     * @var \Magento\Framework\Cache\FrontendInterface
     */
    protected $cache;

    /**
     * @var \Kobens\Core\Model\Exchange\Pair\PairInterface[]
     */
    protected $pairs = [];

    /**
     * @param \Magento\Framework\Cache\FrontendInterface $cacheInterface
     * @param \Kobens\Core\Model\Exchange\Pair\PairInterface[] $pairs
     */
    public function __construct(
        \Magento\Framework\Cache\FrontendInterface $cacheInterface,
        array $pairs = []
        ) {
            $this->cache = $cacheInterface;
            $this->addPairs($pairs);
    }

    /**
     * Add currency pair to the exchange
     *
     * @param \Kobens\Core\Model\Exchange\Pair\PairInterface[] $pairs
     * @throws \Exception
     */
    protected function addPairs($pairs)
    {
        if (!is_array($pairs)) {
            $pairs = [$pairs];
        }
        foreach ($pairs as $pair) {
            if (!$pair instanceof \Kobens\Core\Model\Exchange\Pair\PairInterface) {
                throw new \Exception('Invalid Pair Interface');
            }
            $base = $pair->getBaseCurrency()->getPairIdentity();
            $quote = $pair->getQuoteCurrency()->getPairIdentity();
            $this->pairs[$base.'/'.$quote] = $pair;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Core\Model\Exchange\ExchangeInterface::getCache()
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * @param string $key
     * @return \Kobens\Core\Model\Exchange\Pair\PairInterface
     */
    public function getPair(string $key) : \Kobens\Core\Model\Exchange\Pair\PairInterface
    {
        if (!isset($this->pairs[$key])) {
            throw new \Exception('Currency Pair "'.$key.'" not found on exchange');
        }
        return $this->pairs[$key];
    }
}