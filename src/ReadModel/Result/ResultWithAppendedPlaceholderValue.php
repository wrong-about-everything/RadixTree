<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Result;

class ResultWithAppendedPlaceholderValue implements Result
{
    private $value;
    private $result;

    public function __construct(string $value, Result $result)
    {
        $this->value = $value;
        $this->result = $result;
    }

    public function isFound(): bool
    {
        return $this->result->isFound();
    }

    public function values(): array
    {
        return array_merge([$this->value], $this->result->values());
    }

    public function nodeId(): int
    {
        return $this->result->nodeId();
    }
}
