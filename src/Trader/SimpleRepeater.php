<?php

namespace Kobens\Exchange\Trader;

use Kobens\Core\Db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSetInterface;
use Kobens\Exchange\Trader\SimpleRepeater\NewOrder;

class SimpleRepeater
{
    const TABLE_NAME = 'trader_simple_repeater';

    const AUTO_ENABLED  = 1;
    const AUTO_DISABLED = 0;

    const STATUS_NEW         = 'new';
    const STATUS_BUY_PLACED  = 'buy_placed';
    const STATUS_BUY_FILLED  = 'buy_filled';
    const STATUS_SELL_PLACED = 'sell_placed';
    const STATUS_SELL_FILLED = 'sell_filled';
    const STATUS_DISABLED    = 'disabled';

    public function getOrdersToPlace() : \Generator
    {
        foreach ($this->getBuys() as $row) {
            yield new NewOrder($row->id, $row->exchange, 'buy', $row->symbol, $row->buy_amount, $row->buy_price);
            unset($row);
        }
        foreach ($this->getSells() as $row) {
            yield new NewOrder($row->id, $row->exchange, 'sell', $row->symbol, $row->sell_amount, $row->sell_price);
            unset($row);
        }
    }

    protected function getBuys() : ResultSetInterface
    {
        return $this->getTable()->select(function(Select $select) {
            $select->where->equalTo('auto_buy', self::AUTO_ENABLED);
            $select->where->in('status', [self::STATUS_NEW, self::STATUS_SELL_FILLED]);
            $select->order(['exchange', 'symbol']);
        });
    }

    protected function getSells() : ResultSetInterface
    {
        return $this->getTable()->select(function(Select $select) {
            $select->where->equalTo('auto_sell', self::AUTO_ENABLED);
            $select->where->equalTo('status', self::STATUS_BUY_FILLED);
            $select->order(['exchange', 'symbol']);
        });
    }

    public function markBuyPlaced(string $id, string $exchange, string $orderId) : void
    {
        $affectedRows = $this->getTable()->update(
            ['last_order_id' => $orderId, 'status' => self::STATUS_BUY_PLACED],
            ['id' => $id]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    public function markBuyFilled(string $id, string $exchange) : void
    {
        $affectedRows = $this->getTable()->update(
            ['status' => self::STATUS_BUY_FILLED],
            ['id' => $id]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    public function markSellPlaced(int $id, string $orderId) : void
    {
        $affectedRows = $this->getTable()->update(
            ['last_order_id' => $orderId, 'status' => self::STATUS_SELL_PLACED],
            ['id' => $id]
        );
        if (!$affectedRows !== 1) {
            // @todo
        }
    }

    public function markSellFilled(int $id) : void
    {
        $affectedRows = $this->getTable()->update(
            ['status' => self::STATUS_SELL_FILLED],
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