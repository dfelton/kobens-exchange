<?php

namespace Kobens\Exchange;

interface PairInterface extends \Kobens\Currency\PairInterface
{
    public function getMinOrderSize() : string;

    public function getMinOrderIncrement() : string;

    public function getMinPriceIncrement() : string;
}

