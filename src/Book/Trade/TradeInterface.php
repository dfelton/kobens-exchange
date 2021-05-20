<?php

declare(strict_types=1);

namespace Kobens\Exchange\Book\Trade;

interface TradeInterface
{
    /**
     * Get the side of the maker for the trade (ask|bid)
     */
    public function getMakerSide(): string;

    /**
     * Get the quantity of base currency traded.
     */
    public function getQuantity(): string;

    /**
     * Return the currency quote amount that the base currency was traded at.
     */
    public function getPrice(): string;

    /**
     * Return the timestamp for the trade.
     */
    public function getTimestamp(): int;
}
