<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node;

use WrongAboutEverything\RadixTree\ReadModel\Node\Type\Type;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

abstract class RadixTreeNode
{
    abstract public function result(string $keyToFind): Result;

    abstract public function equals(RadixTreeNode $node): bool;

    abstract public function type(): Type;
}
