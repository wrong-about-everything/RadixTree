<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class InternalNodeWithPlaceholderPrefixAndLeafNode_OBSOLETE extends InternalNodeWithLeafNode
{
    private $concrete;

    public function __construct(array $keysLengths, array $keyToNode, LeafNode $leafNode)
    {
        $this->concrete = new InternalNodeWithPlaceholderPrefixAndLeafNode(new DefaultInternalNode($keysLengths, $keyToNode), $leafNode);
    }

    public function result(string $keyToFind): Result
    {
        return $this->concrete->result($keyToFind);
    }

    public function equals(RadixTreeNode $node): bool
    {
        return $this->concrete->equals($node);
    }

    public function &keyLengths(): array
    {
        return $this->concrete->keyLengths();
    }

    public function &keyToNodeArray(): array
    {
        return $this->concrete->keyToNodeArray();
    }

    public function leafNode(): LeafNode
    {
        return $this->concrete->leafNode();
    }

    public function internalNode(): InternalNode
    {
        return $this->concrete->internalNode();
    }

    public function hasPlaceholder(): bool
    {
        return true;
    }
}