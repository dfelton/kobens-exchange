<?php
/**
 * TODO: Re-asses the setters here.
 *      - We have an abstract book, and the exchange specific abstract book extends core abstract book...
 *      - No public setters should be necessary.
 *      - Book internally sets the data and public functions fetch it for other processes.
 */
namespace Kobens\Exchange\Book;

interface BookInterface
{
    /**
     * Return the Exchange model for the current book.
     *
     * @return \Kobens\Exchange\ExchangeInterface
     */
    public function getExchange();

    /**
     * Return the Pair model for the current book.
     *
     * @return \Kobens\Exchange\Pair\PairInterface
     */
    public function getPair();

    /**
     * Return the base currency for the current book.
     *
     * @return \Kobens\Currency\CurrencyInterface
     */
    public function getBaseCurrency();

    /**
     * Return the quote currency for the current quote.
     *
     * @return \Kobens\Currency\CurrencyInterface
     */
    public function getQuoteCurrency();

    /**
     * Return the market's order book.
     *
     * @return array
     */
    public function getBook();

    /**
     * Get the remaining amount on the book for the given maker side and quote.
     *
     * @param string $makerSide
     * @param float $quote
     */
    public function getRemaining(string $makerSide, $quote);

    /**
     * @return \Kobens\Exchange\Book\Trade\TradeInterface
     */
    public function getLastTrade();

    /**
     * Return the current asking price on the order book.
     *
     * @return string
     */
    public function getAskPrice() : string;

    /**
     * Return the current bid price
     *
     * @return string
     */
    public function getBidPrice() : string;

    /**
     * @return string
     */
    public function getSpread() : string;

}