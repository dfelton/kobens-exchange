<?php

namespace Kobens\Exchange\Book;

use Kobens\Currency\CurrencyInterface;
use Kobens\Exchange\Book\Trade\TradeInterface;
use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Pair\PairInterface;

interface BookInterface
{
    /**
     * Return the Exchange model for the current book.
     */
    public function getExchange() : ExchangeInterface;

    /**
     * Return the Pair model for the current book.
     */
    public function getPair() : PairInterface;

    /**
     * Return the base currency for the current book.
     */
    public function getBaseCurrency() : CurrencyInterface;

    /**
     * Return the quote currency for the current quote.
     */
    public function getQuoteCurrency() : CurrencyInterface;

    /**
     * Return the market's order book.
     */
    public function getBook() : array;

    /**
     * Get the remaining amount on the book for the given maker side and quote.
     */
    public function getRemaining(string $makerSide, string $quote) : string;

    /**
     * Return details about the last trade that occurred on book
     */
    public function getLastTrade() : TradeInterface;

    /**
     * Return the current asking price on the order book.
     */
    public function getAskPrice() : string;

    /**
     * Return the current bid price
     */
    public function getBidPrice() : string;

    /**
     * Return the book's spread
     */
    public function getSpread() : string;

}