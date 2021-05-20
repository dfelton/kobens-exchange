<?php

declare(strict_types=1);

namespace KobensTest\Exchange\Assets;

use Kobens\Currency\Currency;

class PairList
{
    public function toArray()
    {
        return [
            [Currency::getInstance('btc'), Currency::getInstance('eth')],
            [Currency::getInstance('btc'), Currency::getInstance('ltc')],
            [Currency::getInstance('btc'), Currency::getInstance('usd')],
            [Currency::getInstance('btc'), Currency::getInstance('zec')],

            [Currency::getInstance('eth'), Currency::getInstance('btc')],
            [Currency::getInstance('eth'), Currency::getInstance('ltc')],
            [Currency::getInstance('eth'), Currency::getInstance('usd')],
            [Currency::getInstance('eth'), Currency::getInstance('zec')],

            [Currency::getInstance('ltc'), Currency::getInstance('btc')],
            [Currency::getInstance('ltc'), Currency::getInstance('eth')],
            [Currency::getInstance('ltc'), Currency::getInstance('usd')],
            [Currency::getInstance('ltc'), Currency::getInstance('zec')],

            [Currency::getInstance('usd'), Currency::getInstance('btc')],
            [Currency::getInstance('usd'), Currency::getInstance('eth')],
            [Currency::getInstance('usd'), Currency::getInstance('ltc')],
            [Currency::getInstance('usd'), Currency::getInstance('zec')],

            [Currency::getInstance('zec'), Currency::getInstance('btc')],
            [Currency::getInstance('zec'), Currency::getInstance('eth')],
            [Currency::getInstance('zec'), Currency::getInstance('ltc')],
            [Currency::getInstance('zec'), Currency::getInstance('usd')],
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
