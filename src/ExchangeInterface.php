<?php

namespace Kobens\Exchange;

use Kobens\Exchange\Book\BookInterface;
use Kobens\Exchange\Book\Keeper\KeeperInterface;

interface ExchangeInterface
{
    public function getCacheKey() : string;

    public function getPair(string $key) : PairInterface;

    public function getBookKeeper(string $key) : KeeperInterface;

    public function getBook(string $key) : BookInterface;

    public function placeOrder(string $side, string $symbol, string $amount, string $price) : string;
}
