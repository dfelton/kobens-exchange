<?php

namespace Kobens\Exchange\Order;

interface StatusInterface
{
    public function isCancelled(array $metaData) : bool;

    public function isFilled(array $metaData) : bool;

    public function isLive(array $metaData) : bool;
}