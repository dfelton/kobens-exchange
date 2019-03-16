<?php

namespace Kobens\Exchange\Trader;

use Kobens\Core\Db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSetInterface;

class SimpleRepeater
{
    const TABLE_NAME = 'trader_simple_repeater';

    const STATUS_NEW         = 'new';
    const STATUS_BUY_PLACED  = 'buy_placed';
    const STATUS_BUY_FILLED  = 'buy_filled';
    const STATUS_SELL_PLACED = 'sell_placed';
    const STATUS_SELL_FILLED = 'sell_filled';
    const STATUS_DISABLED    = 'disabled';

    public function getOrdersToBuy() : ResultSetInterface
    {
        return $this->getTable()->select(function(Select $select) {
            $select->where->equalTo('auto_buy', 1);
            $select->where->in('status', [
                \Kobens\Exchange\Trader\SimpleRepeater::STATUS_NEW,
                \Kobens\Exchange\Trader\SimpleRepeater::STATUS_SELL_FILLED,
            ]);
            $select->order(['exchange']);
        });
    }

    public function markBuyOrderPlaced(string $id, string $exchange, string $orderId) : void
    {
        $affectedRows = $this->getTable()->update(
            [
                'last_order_id' => $orderId,
                'status' => static::STATUS_BUY_PLACED,
            ],
            ['id' => $id]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    public function markBuyOrderFilled(string $id, string $exchange) : void
    {
        $affectedRows = $this->getTable()->update(
            ['status' => static::STATUS_BUY_FILLED],
            ['id' => $id]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    public function markSellOrderPlaced(string $id, string $exchange, string $orderId) : void
    {
        $affectedRows = $this->getTable()->update(
            [
                'last_order_id' => $orderId,
                'status' => static::STATUS_SELL_PLACED,
            ],
            ['id' => $id, 'exchange' => $exchange]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    public function markSellOrderFilled(string $id, string $exchange) : void
    {
        $affectedRows = $this->getTable()->update(
            ['status' => static::STATUS_SELL_FILLED],
            ['id' => $id]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    protected function getTable() : TableGateway
    {
        return new TableGateway(static::TABLE_NAME, (new Db())->getAdapter());
    }
}