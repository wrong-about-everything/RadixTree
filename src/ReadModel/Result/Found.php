<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Result;

class Found implements Result
{
    private $placeholderValues;
    private $nodeId;

    public function __construct(array $placeholderValues, int $nodeId)
    {
        $this->placeholderValues = $placeholderValues;
        $this->nodeId = $nodeId;
    }

    public function isFound(): bool
    {
        return true;
    }

    public function values(): array
    {
        return $this->placeholderValues;
    }

    public function nodeId(): int
    {
        return $this->nodeId;
    }
}
