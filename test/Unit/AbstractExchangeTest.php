<?php

namespace KobensTest\Exchange\Unit;

use PHPUnit\Framework\TestCase;
use Kobens\Exchange\{AbstractExchange, PairInterface};
use Kobens\Exchange\Exception\Exception;
use KobensTest\Exchange\Assets\{Pair, PairList};
use Zend\Cache\Storage\StorageInterface;
use Zend\Cache\Storage\Adapter\Filesystem;

class AbstractExchangeTest extends TestCase
{
    /**
     * @var AbstractExchange
     */
    protected $exchange;

    protected function setUp() : void
    {
        $pairs = [];
        $pairList = new PairList();
        foreach ($pairList->getAllCurrencyPairs() as $pair) {
            $pairs[] = new Pair($pair[0], $pair[1]);
        }
        $cache = new Filesystem([
            'cache_dir' => '/tmp/kobens/kobens-exchange-test'
        ]);
        $this->exchange = $this->getMockForAbstractClass(
            AbstractExchange::class,
            [$cache, $pairs]
        );
    }

    /**
     * @dataProvider \KobensTest\Exchange\Assets\PairList::getPairKeys
     * @see \KobensTest\Exchange\Assets\PairList::getPairKeys()
     */
    public function testGetPair(string $key)
    {
        $this->assertInstanceOf(
            PairInterface::class,
            $this->exchange->getPair($key)
        );;
    }

    public function testGetPairThrowsException() : void
    {
        foreach (['foo', 'bar'] as $key) {
            $this->expectException(Exception::class);
            $this->exchange->getPair($key);
        }
    }

    public function testGetCache() : void
    {
        $this->assertInstanceOf(StorageInterface::class, $this->exchange->getCache());
    }

}