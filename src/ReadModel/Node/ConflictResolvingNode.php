<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Node\Type\ConflictResolving;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\Type;
use WrongAboutEverything\RadixTree\ReadModel\Result\NonFound;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

abstract class ConflictResolvingNode extends RadixTreeNode
{
    abstract public function &nodeWithPlaceholders(): RadixTreeNode;

    abstract public function &nodeWithFixedKeys(): RadixTreeNode;

    public function result(string $keyToFind): Result
    {
        foreach ($this->nodes() as $node) {
            $result = $node->result($keyToFind);
            if ($result->isFound()) {
                return $result;
            }
        }

        return new NonFound();
    }

    public function type(): Type
    {
        return new ConflictResolving();
    }

    public function equals(RadixTreeNode $node): bool
    {
        if (!$node instanceof ConflictResolvingNode) {
            return false;
        }

        /* @var $node ConflictResolvingNode */
        foreach ($this->nodes() as $i => $currentNode) {
            if (!$currentNode->equals($node->nodes()[$i])) {
                return false;
            }
        }

        return true;
    }

    private function nodes(): array
    {
        return [$this->nodeWithFixedKeys(), $this->nodeWithPlaceholders()];
    }
}