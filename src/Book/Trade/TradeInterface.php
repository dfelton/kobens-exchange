<?php

namespace Kobens\Exchange\Book\Trade;

interface TradeInterface
{

    /**
     * Get the side of the maker for the trade (ask|bid)
     *
     * @return string
     */
    public function getMakerSide() : string;

    /**
     * Get the quantity of base currency traded.
     *
     * @return string
     */
    public function getQuantity() : string;

    /**
     * Return the currency quote amount that the base currency was traded at.
     *
     * @return string
     */
    public function getPrice() : string;

    /**
     * Return the timestamp for the trade.
     *
     * @return int
     */
    public function getTimestamp() : int;

}