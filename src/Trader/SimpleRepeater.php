<?php

namespace Kobens\Exchange\Trader;

use Kobens\Core\Db;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class SimpleRepeater
{
    const TABLE_NAME = 'trader_simple_repeater';

    const STATUS_NEW         = 'new';
    const STATUS_BUY_PLACED  = 'buy_placed';
    const STATUS_BUY_FILLED  = 'buy_filled';
    const STATUS_SELL_PLACED = 'sell_placed';
    const STATUS_SELL_FILLED = 'sell_filled';
    const STATUS_DISABLED    = 'disabled';

    public function getOrdersToBuy()
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

    protected function getTable() : TableGateway
    {
        return new TableGateway(static::TABLE_NAME, (new Db())->getAdapter());
    }
}