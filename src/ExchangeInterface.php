<?php 

namespace Kobens\Exchange;

interface ExchangeInterface
{
    /**
     * @return string
     */
    public function getCacheKey();
    
    /**
     * @param string $key
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair($key) : \Kobens\Exchange\Pair\PairInterface;
    
//     /**
//      * @return \Magento\Framework\Cache\FrontendInterface
//      */
//     public function getCache();
}