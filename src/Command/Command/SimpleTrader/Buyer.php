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
    protected $simpleRepeater;

    protected $hasReportedUpToDate = false;
    protected $secondsToClearScreen = 5;
    protected $secondsSinceClear = 0;

    protected function configure()
    {
        $this->setName('exchange:trader:simple-repeater:buyer');
        $this->setDescription('Performs buys for the simple repeater trader.');
    }

    protected function initialize($input, $output)
    {
        $this->simpleRepeater = new SimpleRepeater();
        $this->exchangeMapper = new Mapper();
        $this->log = new Logger();
        $this->log->pushHandler(new StreamHandler(
            \sprintf(
                '%s/var/log/exchange_simple_trader_buyer_%d.log',
                (new Config())->getRoot(),
                \getmypid()
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
        $resultSet = $this->simpleRepeater->getOrdersToBuy();
        if ($resultSet->count() === 0) {
            if (   $this->hasReportedUpToDate === false
                || $this->secondsSinceClear > $this->secondsToClearScreen
            ) {
                $this->hasReportedUpToDate = true;
                $this->secondsSinceClear = 0;
                $this->clearTerminal($output);
                $output->write('All active buy orders up to date.');
            }
            $this->secondsSinceClear++;
        } else {
            $this->hasReportedUpToDate = false;
            $this->secondsSinceClear = 0;
            $this->clearTerminal($output);
            $output->write('Detected buy orders ready to place.');
            $this->placeOrders($resultSet, $output);
        }

        \sleep(1);
        $output->write('.');
        $this->loop($output);
    }

    protected function placeOrders(ResultSet $resultSet, OutputInterface $output)
    {
        for ($i=0;$i<=5;$i++) {
            $output->write('.');
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