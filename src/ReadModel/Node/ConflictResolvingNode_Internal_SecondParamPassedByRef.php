<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

class ConflictResolvingNode_Internal_SecondParamPassedByRef extends ConflictResolvingNode
{
    private $fixed;
    private $withPlaceholders;

    public function __construct(RadixTreeNode $fixed, RadixTreeNode &$withPlaceholders)
    {
        $this->fixed = $fixed;
        $this->withPlaceholders =& $withPlaceholders;
    }

    public function &nodeWithPlaceholders(): RadixTreeNode
    {
        return $this->withPlaceholders;
    }

    public function &nodeWithFixedKeys(): RadixTreeNode
    {
        return $this->fixed;
    }
}