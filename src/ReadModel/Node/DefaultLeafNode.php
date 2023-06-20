<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Found;
use WrongAboutEverything\RadixTree\ReadModel\Result\NonFound;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class DefaultLeafNode extends LeafNode
{
    private $index;

    public function __construct(int $index)
    {
        $this->index = $index;
    }

    public function result(string $keyToFind): Result
    {
        if (!empty($keyToFind)) {
            return new NonFound();
        }

        return new Found([], $this->index);
    }

    public function id(): int
    {
        return $this->index;
    }
}