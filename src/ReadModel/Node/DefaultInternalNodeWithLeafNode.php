<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class DefaultInternalNodeWithLeafNode extends InternalNodeWithLeafNode
{
    private $internalNode;
    private $leafNode;

    public function __construct(InternalNode $internalNode, LeafNode $leafNode)
    {
        $this->internalNode = $internalNode;
        $this->leafNode = $leafNode;
    }

    public function result(string $keyToFind): Result
    {
        if (empty($keyToFind)) {
            return $this->leafNode->result($keyToFind);
        }

        return $this->internalNode->result($keyToFind);
    }

    public function equals(RadixTreeNode $node): bool
    {
        if (get_class($node) !== get_class($this)) {
            return false;
        }

        /* @var $node InternalNodeWithLeafNode */
        return $this->leafNode()->equals($node->leafNode()) && $this->internalNode()->equals($node->internalNode());
    }

    public function &keyToNodeArray(): array
    {
        return $this->internalNode()->keyToNodeArray();
    }

    public function &keyLengths(): array
    {
        return $this->internalNode()->keyLengths();
    }

    public function internalNode(): InternalNode
    {
        return $this->internalNode;
    }

    public function leafNode(): LeafNode
    {
        return $this->leafNode;
    }

    public function hasPlaceholder(): bool
    {
        return false;
    }
}