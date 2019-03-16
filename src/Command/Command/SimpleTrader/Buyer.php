<?php

namespace Kobens\Exchange\Command\Command\SimpleTrader;

use Kobens\Core\Command\Traits\Traits;
use Kobens\Core\Config;
use Kobens\Exchange\Exchange\Mapper;
use Kobens\Exchange\Trader\SimpleRepeater;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend\Db\ResultSet\ResultSet;

class Buyer extends Command
{
    use Traits;

    /**
     * @var SimpleRepeater
     */
    protected $repeater;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var bool
     */
    protected $hasReportedUpToDate = false;

    protected function configure()
    {
        $this->setName('exchange:trader:simple-repeater:buyer');
        $this->setDescription('Performs buys for the simple repeater trader.');
    }

    protected function initialize($input, $output)
    {
        $this->repeater = new SimpleRepeater();
        $this->mapper = new Mapper();
        $this->log = new Logger('simple_trader_buyer');
        $this->log->pushHandler(new StreamHandler(
            \sprintf(
                '%s/var/log/simple_trade_repeater_buyer.log',
                (new Config())->getRoot()
            ),
            Logger::INFO
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = true;
        do {
            try {
                $this->main($output);
            } catch (\Exception $e) {
                $loop = false;
                $this->logException($e);
            }
        } while ($loop);
    }

    protected function main(OutputInterface $output)
    {
        $resultSet = $this->repeater->getOrdersToBuy();
        if ($resultSet->count() === 0) {
            if ($this->hasReportedUpToDate === false) {
                $this->hasReportedUpToDate = true;
                $output->write(PHP_EOL.'All active buy orders up to date');
            }
        } else {
            $this->hasReportedUpToDate = false;
            $output->write(PHP_EOL.'Detected buy orders ready to place');
            $this->placeOrders($resultSet, $output);
        }
        $output->write('.');
        \sleep(1);
    }

    protected function placeOrders(ResultSet $orders, OutputInterface $output)
    {
        $output->write(PHP_EOL);
        foreach ($orders as $order) {
            $exchange = $this->mapper->getExchange($order->exchange);
            // @todo if price is already cheaper than the simple repeater intended place limit order (non-maker-or-cancel) at price available
            // @todo error handling
            $exchangeOrderId = $exchange->placeOrder(
                'buy',
                $order->symbol,
                $order->buy_amount,
                $order->buy_price
            );
            $this->repeater->markBuyOrderPlaced($order->id, $order->exchange, $exchangeOrderId);
            $output->writeln(\sprintf(
                'Placed buy order on the "%s" pair for amount of "%s" at price of "%s" on "%s" exchange.',
                $order->symbol,
                $order->buy_amount,
                $order->buy_price,
                $order->exchange
            ));
            \sleep(1);
        }
    }

    protected function logException(\Exception $e) : void
    {
        $this->log->warning(\json_encode([
            'errClass' => \get_class($e),
            'errCode' => $e->getCode(),
            'errMessage' => $e->getMessage(),
            'errTrace' => $e->getTraceAsString(),
        ]));
    }

}