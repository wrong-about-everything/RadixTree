<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Result;
use WrongAboutEverything\RadixTree\ReadModel\Result\ResultWithAppendedPlaceholderValue;

class InternalNodeWithPlaceholderPrefixAndLeafNode extends InternalNodeWithLeafNode
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
        $pathParts = explode('/', $keyToFind);
        $placeholderValue = array_shift($pathParts);
        $key = $this->key($pathParts);
        if (empty($key)) {
            return new ResultWithAppendedPlaceholderValue($placeholderValue, $this->leafNode->result($key));
        }

        return
            new ResultWithAppendedPlaceholderValue(
                $placeholderValue,
                $this->internalNode->result($key)
            );
    }

    public function equals(RadixTreeNode $node): bool
    {
        if (get_class($node) !== get_class($this)) {
            return false;
        }

        /* @var $node InternalNodeWithPlaceholderPrefixAndLeafNode */
        return
            $this->internalNode->equals($node->internalNode())
                &&
            $this->leafNode()->equals($node->leafNode())
        ;
    }

    public function &keyLengths(): array
    {
        return $this->internalNode->keyLengths();
    }

    public function &keyToNodeArray(): array
    {
        return $this->internalNode->keyToNodeArray();
    }

    public function leafNode(): LeafNode
    {
        return $this->leafNode;
    }

    public function internalNode(): InternalNode
    {
        return $this->internalNode;
    }

    public function hasPlaceholder(): bool
    {
        return true;
    }

    private function key(array $explodedString): string
    {
        return (new Key($explodedString))->value();
    }
}