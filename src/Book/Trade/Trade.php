<?php

namespace Kobens\Exchange\Book\Trade;

class Trade implements TradeInterface
{

    /**
     * @var string
     */
    protected $makerSide;

    /**
     * @var string
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var int
     */
    protected $timestampms;

    /**
     * @param string $makerSide
     * @param string $quantity
     * @param string $price
     * @param int $timestampms
     * @throws \Kobens\Exchange\Exception\Exception
     */
    public function __construct(
        string $makerSide,
        string $quantity,
        string $price,
        int $timestampms
    ) {
        if (!in_array($makerSide, ['bid','ask'])) {
            throw new \Kobens\Exchange\Exception\Exception(sprintf(
                'Invalid maker side "%s", maker must be "bid" or "ask"',
                $makerSide
            ));
        }
        $this->makerSide = $makerSide;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->timestampms = $timestampms;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Exchange\Book\Trade\TradeInterface::getMakerSide()
     */
    public function getMakerSide() : string
    {
        return $this->makerSide;
    }

    /**
     * {@inheritDoc}
     * @see \Kobens\Exchange\Book\Trade\TradeInterface::getQuantity()
     */
    public function getQuantity() : string
    {
        return $this->quantity;
    }

    /**
     * Return the currency quote amount that the base currency was traded at.
     *
     * @return string
     */
    public function getPrice() : string
    {
        return $this->price;
    }

    /**
     * Return the timestamp for the trade.
     */
    public function getTimestamp() : int
    {
        return $this->timestampms;
    }
}