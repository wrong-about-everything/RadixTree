<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\ReadModel\Node\Type;

abstract class Type
{
    abstract public function value(): int;

    final public function equals(Type $type): bool
    {
        return $this->value() === $type->value();
    }
}