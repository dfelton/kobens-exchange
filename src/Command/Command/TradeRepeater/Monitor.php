<?php

namespace Kobens\Exchange\Command\Command\TradeRepeater;

use Kobens\Core\Command\Traits\Traits;
use Kobens\Core\Config;
use Kobens\Core\Exception\ConnectionException;
use Kobens\Exchange\Exception\LogicException;
use Kobens\Exchange\ExchangeInterface;
use Kobens\Exchange\Exchange\Mapper;
use Kobens\Exchange\TradeStrategies\Repeater;
use Kobens\Exchange\TradeStrategies\Repeater\OrderId;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Monitor extends Command
{
    use Traits;

    /**
     * @var Logger
     */
    private $log;

    /**
     * @var Repeater
     */
    private $repeater;

    /**
     * @var Mapper
     */
    private $mapper;

    protected function configure()
    {
        $this->setName('kobens:exchange:trade-repeater:monitor');
        $this->setDescription('Monitors the status of placed orders for the trade repeater');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Kobens\Core\Exception\LogicException
     */
    protected function initialize($input, $output): void
    {
        $this->repeater = new Repeater();
        $this->mapper   = new Mapper();
        $this->log      = new Logger('simple_trade_monitor');
        $this->log->pushHandler(new StreamHandler(
            \sprintf(
                '%s/var/log/simple_trade_monitor.log',
                (new Config())->getRoot()
            ),
            Logger::DEBUG
        ));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = true;
        $reported = false;
        $lastReported = 0;
        $lastDot = 0;
        do {
            try {
                if ($this->main($output)) {
                    $reported = false;
                }
                $time = \time();
                if (!$reported || $time - $lastReported >= 600) {
                    $output->write(PHP_EOL);
                    $output->write($this->getNow()."\tAll active orders up to date");
                    $reported = true;
                    $lastReported = $time;
                } elseif ($time - $lastDot >= 10) {
                    $output->write('.');
                    $lastDot = $time;
                }
                \sleep(1);
            } catch (ConnectionException $e) {
                $output->write(PHP_EOL);
                $output->writeln($this->getNow()."\t".$e->getMessage());
                $output->write($this->getNow()."\tSleeping 30 seconds");
                for ($i=30; $i>0; $i--) {
                    $output->write('.');
                    \sleep(1);
                }
                $reported = false;
            } catch (\Exception $e) {
                $loop = false;
                $this->logException($e);
            }
        } while ($loop);
    }

    /**
     * @param OutputInterface $output
     * @return bool
     * @throws \Kobens\Exchange\Exception\Exception
     */
    private function main(OutputInterface $output) : bool
    {
        $bool = false;
        foreach ($this->mapper->getKeys() as $key) {
            $exchange = $this->mapper->getExchange($key);
            $aliveOrders = $exchange->getActiveOrderIds();

            /** @var \Kobens\Exchange\Trader\SimpleRepeater\OrderId $order */
            foreach ($this->repeater->getAllActiveOrderIds($key) as $order) {
                if (\in_array($order->exchangeOrderId, $aliveOrders)) {
                    continue;
                }
                $this->monitorOrder($order, $exchange, $output);
            }
        }
        return $bool;
    }

    /**
     * @param OrderId $order
     * @param ExchangeInterface $exchange
     * @param OutputInterface $output
     */
    private function monitorOrder(OrderId $order, ExchangeInterface $exchange, OutputInterface $output) : void
    {
        $status = $exchange->getStatusInterface();
        $meta = $exchange->getOrderMetaData($order->exchangeOrderId);
        switch (true) {
            case $status->isFilled($meta) && $order->status === Repeater::STATUS_BUY_PLACED:
                $this->repeater->markBuyFilled($order->orderId, $order->exchange);
                $output->write(PHP_EOL);
                $output->write(\sprintf(
                    "%s\tMarked <fg=green>buy</> order <fg=yellow>%s</> on the <fg=cyan>%s</> exchange filled",
                    $this->getNow(),
                    $order->exchangeOrderId,
                    ucwords($order->exchange)
                ));
                break;
            case $status->isFilled($meta) && $order->status === Repeater::STATUS_SELL_PLACED:
                $this->repeater->markSellFilled($order->orderId, $order->exchange);
                $output->write(PHP_EOL);
                $output->write(\sprintf(
                    "%s\tMarked <fg=red>sell</> order <fg=yellow>%s</> on the <fg=cyan>%s</> exchange filled",
                    $this->getNow(),
                    $order->exchangeOrderId,
                    ucwords($order->exchange)
                ));
                break;
            case $status->isLive($meta);
                // Latency can cause us to get here.
                break;

            case $status->isOrderCancelled($meta);
                // @todo logging  / comment on record?
                $this->repeater->markDisabled($order->orderId);
                break;
            default:
                $this->log->debug(\json_encode($meta));
                $this->log->debug($order->__toString());
                $this->logException(new LogicException('Unhandled order monitoring'));
                break;
        }
    }

    /**
     * @param \Exception $e
     */
    private function logException(\Exception $e) : void
    {
        $this->log->error('Error Class: '.\get_class($e));
        $this->log->error('Error Code: '.$e->getCode());
        $this->log->error('Error Message: '.$e->getMessage());
        $this->log->error('Stack Trace: '.$e->getTraceAsString());
    }

}
