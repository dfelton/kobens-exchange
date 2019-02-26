<?php

namespace Kobens\Exchange\Pair;

use Kobens\Currency\PairInterface as CurrencyPairInterface;

interface PairInterface extends CurrencyPairInterface
{
    public function getMinOrderSize() : string;

    public function getMinOrderIncrement() : string;

    public function getMinPriceIncrement() : string;
}

