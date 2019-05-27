-- Statuses:
--   NEW
--   BUY_PLACED
--   BUY_FILLED
--   SELL_PLACED
--   COMPLETE
--   CANCELLED

CREATE TABLE `trader_simple_repeater` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key',
  `exchange` varchar(25) NOT NULL COMMENT 'Exchange record belongs to',
  `symbol` varchar(12) NOT NULL COMMENT 'Symbol',
  `status` varchar(24) NOT NULL COMMENT 'Status',
  `auto_buy` tinyint(1)  NOT NULL DEFAULT 0 COMMENT 'Auto suy on next sell completion',
  `auto_sell` tinyint(1)  NOT NULL DEFAULT 0 COMMENT 'Auto sell on next buy completion',
  `last_order_id` varchar(30) DEFAULT NULL COMMENT 'Last order id on exchange',
  `buy_price` varchar(50) NOT NULL COMMENT 'Buy quote currency price',
  `buy_amount` varchar(50) NOT NULL COMMENT 'Buy base currency amount',
  `sell_price` varchar(50) NOT NULL COMMENT 'Sell quote currency price',
  `sell_amount` varchar(50) NOT NULL COMMENT 'Sell Base Amount',
  PRIMARY KEY (`id`),
  KEY `IDX_ORDER_ID` (`last_order_id`),
  KEY `IDX_STATUS` (`status`),
  KEY `IDX_AUTO_BUY` (`auto_buy`),
  KEY `IDX_AUTO_SELL` (`auto_sell`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Trader: Simple Repeater'