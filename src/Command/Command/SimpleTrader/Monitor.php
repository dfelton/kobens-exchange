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

class Monitor extends Command
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

    protected function configure()
    {
        $this->setName('exchange:trader:simple-repeater:monitor');
        $this->setDescription('Monitors the status of placed orders for the simple trade repeater');
    }

    protected function initialize($input, $output)
    {
        $this->repeater = new SimpleRepeater();
        $this->mapper = new Mapper();
        $this->log = new Logger('simple_trade_monitor');
        $this->log->pushHandler(new StreamHandler(
            \sprintf(
                '%s/var/log/simple_trade_monitor.log',
                (new Config())->getRoot()
            ),
            Logger::INFO
        ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $loop = true;
        $reported = false;
        do {
            try {
                if ($this->main($output)) {
                    $reported = false;
                }
                if (!$reported) {
                    $output->write(PHP_EOL);
                    $output->write($this->getNow()."\tAll active orders up to date");
                    $reported = true;
                } elseif (\time() % 600 === 0) {
                    $reported = false;
                } elseif (\time() % 10 === 0) {
                    $output->write('.');
                }
                \sleep(1);
            } catch (\Kobens\Core\Exception\ConnectionException $e) {
                $output->write(PHP_EOL);
                $output->writeln($this->getNow()."\t".$e->getMessage());
                $output->write($this->getNow()."\tSleeping 30 seconds");
                for ($i=30; $i>0; $i--) {
                    $output->write('.');
                    \sleep(1);
                }
            } catch (\Exception $e) {
                $loop = false;
                $this->logException($e);
            }
        } while ($loop);
    }

    protected function main(OutputInterface $output) : bool
    {
        $bool = false;
        foreach ($this->mapper->getKeys() as $key) {
            $exchange = $this->mapper->getExchange($key);
            $aliveOrders = $exchange->getActiveOrderIds();
            $activeSimpleRepeaters = $this->repeater->getAllActiveOrderIds($key);

            /** @var \Kobens\Exchange\Trader\SimpleRepeater\OrderId $order */
            foreach ($activeSimpleRepeaters as $order) {

                if (\in_array($order->exchangeOrderId, $aliveOrders)) {
                    continue;
                }
                $metaData = $exchange->getOrderMetaData($order->exchangeOrderId);
                if ($exchange->isOrderFilled($metaData)) {
                    // @todo get this from the exchange's order meta? rather than last order status here? maybe not due to inconsistent formats across exchanges...
                    switch ($order->status) {
                        case SimpleRepeater::STATUS_BUY_PLACED:
                            $this->repeater->markBuyFilled($order->orderId, $order->exchange);
                            $output->write(PHP_EOL);
                            $output->write(\sprintf(
                                "%s\tBuy order %s from the %s exchange marked filled",
                                $this->getNow(),
                                $order->exchangeOrderId,
                                ucwords($order->exchange)
                            ));
                            break;
                        case SimpleRepeater::STATUS_SELL_PLACED:
                            $this->repeater->markSellFilled($order->orderId, $order->exchange);
                            $output->write(PHP_EOL);
                            $output->write(\sprintf(
                                "%s\tSell order %s from the %s exchange marked filled",
                                $this->getNow(),
                                $order->exchangeOrderId,
                                ucwords($order->exchange)
                            ));
                            break;
                        default:
                            throw new \Exception(\sprintf('Unknown order status. Simple Repeater ID "%"', $order->orderId));
                            break;
                    }

                } elseif ($exchange->isOrderCancelled($metaData)) {
                    $this->repeater->markDisabled($order->orderId);
                } else {
                    // if it is not alive, it is not filled, it is not cancelled, then what is it?
                    throw new \Exception(\sprintf('Unknown Order Status. Simple Repeater ID "%s"', $order->orderId));
                }
            }
        }
        return $bool;
    }

    protected function logException(\Exception $e) : void
    {
        $this->log->error('Error Class: '.\get_class($e));
        $this->log->error('Error Code: '.$e->getCode());
        $this->log->error('Error Message: '.$e->getMessage());
        $this->log->error('Stack Trace: '.$e->getTraceAsString());
    }

}