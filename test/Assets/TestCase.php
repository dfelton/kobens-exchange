<?php

namespace KobensTest\Exchange\Assets;

use Kobens\Exchange\AbstractExchange;
use Kobens\Exchange\Order\StatusInterface;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AbstractExchange
     */
    protected $exchange;

    protected function setUp(): void
    {
        $this->exchange = new class($this->getPairList()) extends AbstractExchange {
            public function __construct(array $pairs) {
                parent::__construct($pairs);
            }
            public function getCacheKey(): string
            {
                return 'fooBar';
            }
            public function placeOrder(string $side, string $symbol, string $amount, string $price): string
            {
                return 'fooBar';
            }
            public function getActiveOrderIds(): array
            {
                return [];
            }
            public function getOrderMetaData(string $orderId): array
            {
                return [];
            }
            public function getStatusInterface(): StatusInterface
            {
                return new class implements StatusInterface {
                    public function isCancelled(array $metaData): bool
                    {
                        return true;
                    }

                    public function isFilled(array $metaData): bool
                    {
                        return true;
                    }

                    public function isLive(array $metaData): bool
                    {
                        return true;
                    }
                };
            }
        };
    }

    protected function getPairList(): array
    {
        $list = new PairList();
        $pairs = [];
        foreach ($list->toArray() as $pair) {
            $pairs[] = new Pair($pair[0], $pair[1]);
        }
        return $pairs;
    }
}
