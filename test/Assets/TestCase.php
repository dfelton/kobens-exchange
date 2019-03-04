<?php

namespace KobensTest\Exchange\Assets;

use Kobens\Exchange\AbstractExchange;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AbstractExchange
     */
    protected $exchange;

    protected function setUp() : void
    {
        $exchange = $this->getMockForAbstractClass(
            AbstractExchange::class,
            [new Cache(), $this->getPairList()],
            '', true, true, true, ['getCacheKey']
        );
        $exchange->method('getCacheKey')->willReturn('foobar');
        $this->exchange = $exchange;
    }

    protected function getPairList() : array
    {
        $list = new PairList();
        $pairs = [];
        foreach ($list->toArray() as $pair) {
            $pairs[] = new Pair($pair[0], $pair[1]);
        }
        return $pairs;
    }
}