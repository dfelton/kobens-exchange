<?php

namespace Kobens\Exchange\Trader\SimpleRepeater;

use Kobens\Exchange\Exception\InvalidArgumentException;

/**
 * @property-read int $id
 * @property-read string $exchange
 * @property-read string $side
 * @property-read string $symbol
 * @property-read string $amount
 * @property-read string $price
 */
class NewOrder
{
    private $id;
    private $exchange;
    private $side;
    private $symbol;
    private $amount;
    private $price;

    /**
     * @param int $id
     * @param string $exchange
     * @param string $side
     * @param string $symbol
     * @param string $amount
     * @param string $price
     */
    public function __construct(int $id, string $exchange, string $side, string $symbol, string $amount, string $price)
    {
        $this->id = $id;
        $this->exchange = $exchange;
        $this->side = $side;
        $this->symbol = $symbol;
        $this->amount = $amount;
        $this->price = $price;
    }

    /**
     * Variable overloading
     *
     * @param  string $name
     * @throws InvalidArgumentException
     * @return mixed
     */
    public function __get($name)
    {
        switch ($name) {
            case 'id':
                return $this->id;
            case 'exchange':
                return $this->exchange;
            case 'side':
                return $this->side;
            case 'symbol':
                return $this->symbol;
            case 'amount':
                return $this->amount;
            case 'price':
                return $this->price;
            default:
                throw new InvalidArgumentException('Not a valid magic property for this object');
        }
    }
}