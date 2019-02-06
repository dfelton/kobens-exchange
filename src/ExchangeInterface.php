<?php 

namespace Kobens\Exchange;

interface ExchangeInterface
{
    /**
     * @return string
     */
    public function getCacheKey() : string;
    
    /**
     * @param string $key
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair($key) : \Kobens\Exchange\Pair\PairInterface;
    
     /**
      * TODO: need a cache interface since pulling this out of Magento
      *
      * @return \Magento\Framework\Cache\FrontendInterface
      */
     public function getCache();
}
