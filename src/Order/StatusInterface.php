<?php

declare(strict_types=1);

namespace Kobens\Exchange\Order;

interface StatusInterface
{
    public function isCancelled(array $metaData): bool;

    public function isFilled(array $metaData): bool;

    public function isLive(array $metaData): bool;
}
