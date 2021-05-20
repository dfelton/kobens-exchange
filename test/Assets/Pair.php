<?php

namespace KobensTest\Exchange\Assets;

use Kobens\Currency\Pair as CurrencyPair;
use Kobens\Exchange\PairInterface;

class Pair extends CurrencyPair implements PairInterface
{
    public function getMinOrderIncrement(): string
    {
        return '0.1';
    }

    public function getMinOrderSize(): string
    {
        return '0.1';
    }

    public function getMinPriceIncrement(): string
    {
        return '0.1';
    }
}
