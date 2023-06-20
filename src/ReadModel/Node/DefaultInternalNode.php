<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use Exception;
use WrongAboutEverything\RadixTree\ReadModel\Result\NonFound;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class DefaultInternalNode extends InternalNode
{
    private $keysLengths;
    private $keyToNode;

    public function __construct(array $keysLengths, array $keyToNode)
    {
        $this->keysLengths = $keysLengths;
        $this->keyToNode = $keyToNode;
    }

    public function result(string $keyToFind): Result
    {
        foreach ($this->keysLengths as $length) {
            $searchedSubstring = substr($keyToFind, 0, $length);
            $node = $this->keyToNode[$searchedSubstring] ?? null;
            if (!is_null($node)) {
                if (strlen($keyToFind) < $length) {
                    throw new Exception(sprintf('Actual length of a current key is less than the one I\'ve found this key with. Key is %s, length is %d', $keyToFind, $length));
                }
                return $node->result(substr($keyToFind, $length));
            }
        }

        return new NonFound();
    }

    public function equals(RadixTreeNode $node): bool
    {
        if (get_class($node) !== get_class($this)) {
            return false;
        }

        /* @var $node DefaultInternalNode */
        return
            $node->keyLengths() === $this->keyLengths()
                &&
            $this->keyToNodesAreEqual($node, $this)
            ;
    }

    public function &keyLengths(): array
    {
        return $this->keysLengths;
    }

    public function &keyToNodeArray(): array
    {
        return $this->keyToNode;
    }

    public function hasPlaceholder(): bool
    {
        return false;
    }

    private function keyToNodesAreEqual(DefaultInternalNode $first, DefaultInternalNode $second): bool
    {
        ksort($first->keyToNodeArray());
        ksort($second->keyToNodeArray());

        if (array_keys($first->keyToNodeArray()) !== array_keys($second->keyToNodeArray())) {
            return false;
        }

        foreach ($first->keyToNodeArray() as $key => $currentNodeOfAFirstNode) {
            if (!$currentNodeOfAFirstNode->equals($second->keyToNodeArray()[$key])) {
                return false;
            }
        }

        return true;
    }
}