<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Result;

interface Result
{
    public function isFound(): bool;

    public function values(): array;

    public function nodeId(): int;
}
