<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node\Type;

class LeafNode extends Type
{
    public function value(): int
    {
        return 0;
    }
}