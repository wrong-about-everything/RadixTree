<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

class DefaultConflictResolvingNode extends ConflictResolvingNode
{
    private $internal;

    public function __construct(RadixTreeNode $fixed, RadixTreeNode $withPlaceholders)
    {
        $this->internal = new ConflictResolvingNode_Internal_SecondParamPassedByRef($fixed,$withPlaceholders);
    }

    public function &nodeWithPlaceholders(): RadixTreeNode
    {
        return $this->internal->nodeWithPlaceholders();
    }

    public function &nodeWithFixedKeys(): RadixTreeNode
    {
        return $this->internal->nodeWithFixedKeys();
    }
}