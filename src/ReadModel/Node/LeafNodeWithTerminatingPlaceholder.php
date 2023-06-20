<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Result;
use WrongAboutEverything\RadixTree\ReadModel\Result\ResultWithAppendedPlaceholderValue;

class LeafNodeWithTerminatingPlaceholder extends LeafNode
{
    private $index;

    public function __construct(int $index)
    {
        $this->index = $index;
    }

    public function result(string $keyToFind): Result
    {
        $pathParts = explode('/', $keyToFind);
        $placeholderValue = array_shift($pathParts);

        return
            new ResultWithAppendedPlaceholderValue(
                $placeholderValue,
                (new DefaultLeafNode($this->index))
                    ->result(
                        (new Key($pathParts))->value()
                    )
            );
    }

    public function id(): int
    {
        return $this->index;
    }
}