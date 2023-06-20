<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Result\Result;
use WrongAboutEverything\RadixTree\ReadModel\Result\ResultWithAppendedPlaceholderValue;

class InternalNodeWithPlaceholderPrefix extends InternalNode
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
        $pathParts = explode('/', $keyToFind);

        return
            new ResultWithAppendedPlaceholderValue(
                array_shift($pathParts),
                (new DefaultInternalNode($this->keysLengths, $this->keyToNode))
                    ->result(
                        (new Key($pathParts))->value()
                    )
            );
    }

    public function equals(RadixTreeNode $node): bool
    {
        if (get_class($node) !== get_class($this)) {
            return false;
        }

        /* @var $node InternalNodeWithPlaceholderPrefix */
        return
            (new DefaultInternalNode($this->keysLengths, $this->keyToNode))
                ->equals(
                    new DefaultInternalNode($node->keyLengths(), $node->keyToNodeArray())
                )
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
        return true;
    }
}