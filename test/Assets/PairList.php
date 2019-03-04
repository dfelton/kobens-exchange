<?php

namespace KobensTest\Exchange\Assets;

use Kobens\Currency\Crypto\{Bitcoin as BTC,Ethereum as ETH,Litecoin as LTC,Zcash as ZEC};
use Kobens\Currency\Fiat\USD;

class PairList
{
    public function toArray()
    {
        return [
            [new BTC(), new ETH()],
            [new BTC(), new LTC()],
            [new BTC(), new USD()],
            [new BTC(), new ZEC()],

            [new ETH(), new BTC()],
            [new ETH(), new LTC()],
            [new ETH(), new USD()],
            [new ETH(), new ZEC()],

            [new LTC(), new BTC()],
            [new LTC(), new ETH()],
            [new LTC(), new USD()],
            [new LTC(), new ZEC()],

            [new USD(), new BTC()],
            [new USD(), new ETH()],
            [new USD(), new LTC()],
            [new USD(), new ZEC()],

            [new ZEC(), new BTC()],
            [new ZEC(), new ETH()],
            [new ZEC(), new LTC()],
            [new ZEC(), new USD()],
        ];

    }

    public function getPairKeys()
    {
        return [
            ['btceth'],
            ['btcltc'],
            ['btcusd'],
            ['btczec'],

            ['ethbtc'],
            ['ethltc'],
            ['ethusd'],
            ['ethzec'],

            ['ltcbtc'],
            ['ltceth'],
            ['ltcusd'],
            ['ltczec'],

            ['usdbtc'],
            ['usdeth'],
            ['usdltc'],
            ['usdzec'],

            ['zecbtc'],
            ['zeceth'],
            ['zecltc'],
            ['zecusd'],
        ];
    }
}