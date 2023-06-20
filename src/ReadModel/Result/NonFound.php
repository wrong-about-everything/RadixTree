<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Result;

use Exception;

class NonFound implements Result
{
    public function isFound(): bool
    {
        return false;
    }

    public function values(): array
    {
        throw new Exception('Non-found result can\'t have placeholder values');
    }

    public function nodeId(): int
    {
        throw new Exception('Non-found result can\'t have node id');
    }
}
