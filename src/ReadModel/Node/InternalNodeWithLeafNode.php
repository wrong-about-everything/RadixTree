<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

abstract class InternalNodeWithLeafNode extends InternalNode
{
    abstract public function internalNode(): InternalNode;

    abstract public function leafNode(): LeafNode;
}