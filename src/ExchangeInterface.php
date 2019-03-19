<?php

namespace Kobens\Exchange;

use Kobens\Exchange\Book\BookInterface;
use Kobens\Exchange\Book\Keeper\KeeperInterface;

/**
 * @todo organize some of these into a more strictly purposed interface, that this interface is capable of returning
 */
interface ExchangeInterface
{
    public function getCacheKey() : string;

    public function getPair(string $key) : PairInterface;

    public function getBookKeeper(string $key) : KeeperInterface;

    public function getBook(string $key) : BookInterface;

    public function placeOrder(string $side, string $symbol, string $amount, string $price) : string;

    public function getActiveOrderIds() : array;

    public function getOrderMetaData(string $orderId) : array;

    public function isOrderCancelled(array $metaData) : bool;

    public function isOrderFilled(array $metaData) : bool;
}
