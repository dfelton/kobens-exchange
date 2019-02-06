<?php

namespace Kobens\Exchange\Pair;

interface PairInterface
{
    /**
     * @return string
     */
    public function getPairSymbol() : string;
    
    /**
     * @return \Kobens\Currency\CurrencyInterface
     */
    public function getBaseCurrency();
    
    /**
     * @return \Kobens\Currency\CurrencyInterface
     */
    public function getQuoteCurrency();
    
    /**
     * Return the equivilant base currency quantity based
     * off the given quote currency quantity and quote
     * currency rate.
     *
     * @param string $quoteQty
     * @param string $quoteRate
     * @return string
     */
    public function getBaseQty(string $quoteQty, string $quoteRate) : string;
    
    /**
     * Return the equivilant quote currency quantity based
     * off the given base currency quantity and quote
     * currency rate.
     *
     * @param string $baseQty
     * @param string $quoteRate
     * @return string
     */
    public function getQuoteQty(string $baseQty, string $quoteRate) : string;
    
}

