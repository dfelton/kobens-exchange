<?php

namespace Kobens\Exchange\Book;

use Kobens\Exchange\Book\Trade\TradeInterface;

interface BookInterface
{
    /**
     * Return the market's order book.
     */
    public function getRawBookData() : array;

    /**
     * Get the remaining amount on the book for the given maker side and quote.
     */
    public function getRemaining(string $makerSide, string $quote) : string;

    /**
     * Return details about the last trade that occurred on book
     */
    public function getLastTrade() : ?TradeInterface;

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

    public function getSymbol() : string;

}