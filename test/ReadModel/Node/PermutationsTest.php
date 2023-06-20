<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\Permutations;

class PermutationsTest extends TestCase
{
    public function test()
    {
        $input = [0 => 'a', 1 => 'b', 2 => 'c', 3 => 'd'];

        $this->assertEquals(
            [
                ['a', 'b', 'c', 'd'],
                ['a', 'b', 'd', 'c'],
                ['a', 'c', 'b', 'd'],
                ['a', 'c', 'd', 'b'],
                ['a', 'd', 'b', 'c'],
                ['a', 'd', 'c', 'b'],
                ['b', 'a', 'c', 'd'],
                ['b', 'a', 'd', 'c'],
                ['b', 'c', 'a', 'd'],
                ['b', 'c', 'd', 'a'],
                ['b', 'd', 'a', 'c'],
                ['b', 'd', 'c', 'a'],
                ['c', 'a', 'b', 'd'],
                ['c', 'a', 'd', 'b'],
                ['c', 'b', 'a', 'd'],
                ['c', 'b', 'd', 'a'],
                ['c', 'd', 'a', 'b'],
                ['c', 'd', 'b', 'a'],
                ['d', 'a', 'b', 'c'],
                ['d', 'a', 'c', 'b'],
                ['d', 'b', 'a', 'c'],
                ['d', 'b', 'c', 'a'],
                ['d', 'c', 'a', 'b'],
                ['d', 'c', 'b', 'a'],
            ],
            (new Permutations($input))->value()
        );
    }
}