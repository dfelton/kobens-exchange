<?php

declare(strict_types=1);

namespace Kobens\Exchange\Book\Keeper;

interface KeeperInterface
{
    public function openBook(): void;
}
