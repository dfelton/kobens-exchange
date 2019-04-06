<?php

namespace Kobens\Exchange\Book\Trade;

final class Trade implements TradeInterface
{

    /**
     * @var string
     */
    private $makerSide;

    /**
     * @var string
     */
    private $quantity;

    /**
     * @var string
     */
    private $price;

    /**
     * @var int
     */
    private $timestampms;

    /**
     * @throws \Kobens\Exchange\Exception\Exception
     */
    public function __construct(
        string $makerSide,
        string $quantity,
        string $price,
        int $timestampms
    ) {
        if (!\in_array($makerSide, ['bid','ask'])) {
            throw new \Kobens\Exchange\Exception\Exception(\sprintf(
                'Invalid maker side "%s", maker must be "bid" or "ask"',
                $makerSide
            ));
        }
        $this->makerSide = $makerSide;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->timestampms = $timestampms;
    }

    public function getMakerSide() : string
    {
        return $this->makerSide;
    }

    public function getQuantity() : string
    {
        return $this->quantity;
    }

    public function getPrice() : string
    {
        return $this->price;
    }

    public function getTimestamp() : int
    {
        return $this->timestampms;
    }

}
