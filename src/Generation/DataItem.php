<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Generation;

class DataItem
{
    private $key;
    private $id;

    public function __construct(string $key, int $id)
    {
        $this->key = $key;
        $this->id = $id;
    }

    public function key(): string
    {
        return $this->key;
    }

    public function id(): int
    {
        return $this->id;
    }
}