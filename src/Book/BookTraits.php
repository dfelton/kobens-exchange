<?php

declare(strict_types=1);

namespace Kobens\Exchange\Book;

use Kobens\Exchange\Exception\InvalidBookSideException;

trait BookTraits
{
    public function validateSide(string $side): void
    {
        if ($side !== 'bid' && $side !== 'ask') {
            throw new InvalidBookSideException(\sprintf(
                'Book sides available are "bid" or "ask", "%s" requested.',
                $side
            ));
        }
    }
}
