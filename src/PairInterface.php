<?php

namespace Kobens\Exchange;

/**
 * @property-read string $minOrderSize
 * @property-read string $minOrderIncrement
 * @property-read string $minPriceIncrement
 */
interface PairInterface extends \Kobens\Currency\PairInterface
{
    public function getMinOrderSize() : string;

    public function getMinOrderIncrement() : string;

    public function getMinPriceIncrement() : string;
}

