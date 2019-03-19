<?php

namespace Kobens\Exchange\Command\Command\SimpleTrader;

use Kobens\Core\Config;
use Kobens\Exchange\Exception\LogicException;
use Kobens\Exchange\Exchange\Mapper;
use Kobens\Exchange\Trader\SimpleRepeater;
use Kobens\Exchange\Trader\SimpleRepeater\NewOrder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Kobens\Exchange\Exception\Order\MakerOrCancelWouldTakeException;

class OrderPlacer extends Command
{
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
        $this->setName('exchange:trader:simple-repeater:order-placer');
        $this->setDescription('Places buy and sell orders for the simple trade repeater.');
    }

    protected function initialize($input, $output)
    {
        $this->repeater = new SimpleRepeater();
        $this->mapper = new Mapper();
        $this->log = new Logger('simple_trade_repeater');
        $this->log->pushHandler(new StreamHandler(
            \sprintf(
                '%s/var/log/simple_trade_repeater.log',
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
                    $output->write(PHP_EOL.'All active orders up to date');
                    $reported = true;
                }
                if (\time() % 10 === 0) {
                    $output->write('.');
                }
                \sleep(1);
            } catch (\Exception $e) {
                $loop = false;
                $this->logException($e);
            }
        } while ($loop);
    }

    protected function main(OutputInterface $output) : bool
    {
        $bool = false;
        /** @var NewOrder $order */
        foreach ($this->repeater->getOrdersToPlace() as $order) {
            // @todo fetch book, check price, don't attempt to place if not appropriate
            try {
                $exchangeOrderId = $this->place($order);
                $this->updateSimpleTrader($order, $exchangeOrderId);
                $this->reportOrder($output, $order);
                $bool = true;
            } catch (MakerOrCancelWouldTakeException $e) {
                // @todo remove once checking of price is in place
                $this->repeater->markDisabled($order->id);
            }
            // @todo fetch exchange rate limit (add to interface); compare request time, dynamically determine sleep (if any)
            \usleep(150000);
        }
        return $bool;
    }

    protected function updateSimpleTrader(NewOrder $order, string $exchangeOrderId) : void
    {
        switch ($order->side) {
            case 'buy':
            case 'bid':
                $this->repeater->markBuyPlaced($order->id, $order->exchange, $exchangeOrderId);
                break;
            case 'sell':
            case 'ask':
                $this->repeater->markSellPlaced($order->id, $exchangeOrderId);
                break;
            default:
                throw new LogicException(\sprintf('Invalid order side "%s".', $order->side));
                break;
        }
    }

    protected function place(NewOrder $order) : string
    {
        $exchange = $this->mapper->getExchange($order->exchange);
        return $exchange->placeOrder($order->side, $order->symbol, $order->amount, $order->price);
    }

    protected function reportOrder(OutputInterface $output, NewOrder $order) : void
    {
        $output->write(PHP_EOL);
        $output->write(\sprintf(
            'Placing %s order on the %s pair for amount of "%s" at price of "%s" on "%s" exchange.',
            $order->side, $order->symbol, $order->amount, $order->price, \ucwords($order->exchange)
        ));
    }

    protected function logException(\Exception $e) : void
    {
        $this->log->error('Error Class: '.\get_class($e));
        $this->log->error('Error Code: '.$e->getCode());
        $this->log->error('Error Message: '.$e->getMessage());
        $this->log->error('Stack Trace: '.$e->getTraceAsString());
    }

}