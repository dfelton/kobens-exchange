<?php

namespace KobensTest\Exchange\Unit;

use Kobens\Exchange\PairInterface;
use Kobens\Exchange\Exception\Exception;
use KobensTest\Exchange\Assets\TestCase;
use Zend\Cache\Storage\StorageInterface;

class AbstractExchangeTest extends TestCase
{
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

    public function testGetPairThrowsException(): void
    {
        foreach (['foo', 'bar'] as $key) {
            $this->expectException(Exception::class);
            $this->exchange->getPair($key);
        }
    }
}
