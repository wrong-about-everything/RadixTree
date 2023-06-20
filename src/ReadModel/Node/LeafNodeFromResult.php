<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class LeafNodeFromResult extends LeafNode
{
    private $result;

    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    public function result(string $keyToFind): Result
    {
        return $this->result;
    }

    public function id(): int
    {
        return $this->result->nodeId();
    }
}