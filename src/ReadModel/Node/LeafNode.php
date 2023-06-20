<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Node\Type\LeafNode as LeafNodeType;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\Type;

abstract class LeafNode extends RadixTreeNode
{
    final function type(): Type
    {
        return new LeafNodeType();
    }

    public function equals(RadixTreeNode $node): bool
    {
        if (get_class($this) !== get_class($node)) {
            return false;
        }

        /* @var $node LeafNode */

        return $node->id() === $this->id();
    }

    abstract public function id(): int;
}