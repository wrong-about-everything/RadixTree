<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

class Key
{
    private $parts;

    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    public function value(): string
    {
        return
            empty($this->parts)
                ? ''
                : '/' . implode('/', $this->parts)
            ;
    }
}