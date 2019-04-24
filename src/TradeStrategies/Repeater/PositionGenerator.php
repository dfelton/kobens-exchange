<?php

namespace Kobens\Exchange\TradeStrategies\Repeater;

use Kobens\Exchange\Exchange\Mapper;
use Kobens\Exchange\PairInterface;

final class PositionGenerator
{
    /**
     * @var string
     */
    private $fee;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var PairInterface
     */
    private $pair;

    public function __construct($exchangeKey, $pairKey)
    {
        $this->mapper = new Mapper();
        $this->pair = $this->mapper->getExchange($exchangeKey)->getPair($pairKey);
    }

    public function generate(
        string $exchangeKey,
        string $symbol,
        string $amountPerBuy,
        string $startPrice,
        string $sellAtGainPercent,
        string $stopPrice
    ) {
    }

    /**
     * @todo move this to kobens/kobens-currency
     */
    public function getFee(string $amount) : string
    {
        $result = \bcmul($amount, $this->fee, $this->getBcmulPrecision($amount, $this->fee));
        $result = \rtrim($result, '0');
        $result = \rtrim($result, '.');
        return $result;
    }

}

