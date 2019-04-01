<?php

namespace Kobens\Exchange\Trader\SimpleRepeater;

use Kobens\Exchange\Exchange\Mapper;
use Kobens\Exchange\PairInterface;

class PositionGenerator
{
    /**
     * @var string
     */
    protected $fee;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var PairInterface
     */
    protected $pair;


    public function __construct($exchangeKey, $pairKey)
    {
        $mapper = new Mapper();
        $exchange = $mapper->getExchange($exchangeKey);
        $this->pair = $exchange->getPair($pairKey);


        $this->mapper = $mapper;
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

