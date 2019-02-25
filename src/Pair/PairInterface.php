<?php

namespace Kobens\Exchange\Pair;

use Kobens\Currency\PairInterface as CurrencyPairInterface;

interface PairInterface extends CurrencyPairInterface
{
    // @todo minimum order amount
    // @todo minimum base increment
    // @todo minimum quote increment
}

