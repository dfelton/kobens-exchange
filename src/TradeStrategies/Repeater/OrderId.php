<?php

declare(strict_types=1);

namespace Kobens\Exchange\TradeStrategies\Repeater;

use Kobens\Exchange\Exception\InvalidArgumentException;

/**
 * @property-read int $orderId
 * @property-read string $exchangeOrderId
 * @property-read string $exchange
 * @property-read string $status
 */
final class OrderId
{
    private $orderId;
    private $exchangeOrderId;
    private $exchange;
    private $status;

    /**
     * @param int $orderId
     * @param string $exchange
     */
    public function __construct(int $orderId, string $exchangeOrderId, string $exchange, string $status)
    {
        $this->orderId = $orderId;
        $this->exchangeOrderId = $exchangeOrderId;
        $this->exchange = $exchange;
        $this->status = $status;
    }

    public function __toString()
    {
        return \json_encode([
            'orderId' => $this->orderId,
            'exchangeOrderId' => $this->exchangeOrderId,
            'exchange' => $this->exchange,
            'status' => $this->status,
        ]);
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
            case 'orderId':
                return $this->orderId;
            case 'exchangeOrderId':
                return $this->exchangeOrderId;
            case 'exchange':
                return $this->exchange;
            case 'status':
                return $this->status;
            default:
                throw new InvalidArgumentException('Not a valid magic property for this object');
        }
    }
}
