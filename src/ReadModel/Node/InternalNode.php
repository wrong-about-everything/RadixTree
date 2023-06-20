<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Node\Type\InternalNode as InternalNodeType;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\Type;

abstract class InternalNode extends RadixTreeNode
{
    /* @return RadixTreeNode[] */
    abstract public function &keyToNodeArray(): array;

    abstract public function &keyLengths(): array;

    public function type(): Type
    {
        return new InternalNodeType();
    }

    abstract function hasPlaceholder(): bool;
}