<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeFromResult;
use WrongAboutEverything\RadixTree\ReadModel\Result\Found;
use WrongAboutEverything\RadixTree\ReadModel\Result\NonFound;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class DefaultInternalNodeTest extends TestCase
{
    public function testFoundResultForNonEmptyKey()
    {
        $result =
            (new DefaultInternalNode(
                [1, 5],
                [
                    'a' =>
                        new LeafNodeFromResult(
                            new Found(['vasya', 'fedya'], 1)
                        ),
                    'barry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            ))
                ->result('askdjghskldjfh');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['vasya', 'fedya'], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    /**
     * This is a strange case which is barely useful in reality.
     * Basically, it means that LeafNode is always found. But if the searched $key is not empty, its result is not found.
     *
     * If you think you want to use it, you actually need an InternalNodeWithLeafNode class.
     */
    public function testFoundResultForEmptyKey()
    {
        $result =
            (new DefaultInternalNode(
                [0, 5],
                [
                    '' =>
                        new LeafNodeFromResult(
                            new Found(['vasya', 'fedya'], 1)
                        ),
                    'barry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            ))
                ->result('');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['vasya', 'fedya'], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    public function testNonFoundResult()
    {
        $result =
            (new DefaultInternalNode(
                [1, 5],
                [
                    'b' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 1)
                        ),
                    'curry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            ))
                ->result('aasdadsdgfg');

        $this->assertFalse($result->isFound());
        $this->assertWhenParamsMethodIsCalledThenAnExceptionWasThrown($result);
        $this->assertWhenClosureMethodIsCalledThenAnExceptionWasThrown($result);
    }

    public function testWhenPrecalculatedKeyLengthIsGreaterThanActualKeyLengthThenExceptionIsThrown()
    {
        try {
            (new DefaultInternalNode(
                [10],
                [
                    'curry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 1)
                        ),
                ]
            ))
                ->result('curry');
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('Exception should have been thrown');
    }

    public function testTwoInternalNodesAreEqual()
    {
        $this->assertTrue(
            $this->internalNode()->equals($this->internalNode())
        );
    }

    public function testGivenNodesOfTwoDifferentClassesThenNodesAreNotEqual()
    {
        $this->assertFalse(
            $this->internalNode()->equals(new LeafNodeFromResult(new NonFound()))
        );
    }

    public function testGivenDefaultInternalNodesWithDifferentKeysLengthsThenNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultInternalNode(
                [1, 5],
                [
                    'b' => new LeafNodeFromResult(new NonFound()),
                    'curry' => new LeafNodeFromResult(new Found([], 1)),
                ]
            ))
                ->equals(
                    new DefaultInternalNode(
                        [1, 6],
                        [
                            'b' => new LeafNodeFromResult(new NonFound()),
                            'curry' => new LeafNodeFromResult(new Found([], 1)),
                        ]
                    )
                )
        );
    }

    public function testGivenDefaultInternalNodesWithDifferentKeysThenNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultInternalNode(
                [1, 5],
                [
                    'b' => new LeafNodeFromResult(new NonFound()),
                    'curry' => new LeafNodeFromResult(new Found([], 1)),
                ]
            ))
                ->equals(
                    new DefaultInternalNode(
                        [1, 5],
                        [
                            'b' => new LeafNodeFromResult(new NonFound()),
                            'hurry' => new LeafNodeFromResult(new Found([], 1)),
                        ]
                    )
                )
        );
    }

    public function testGivenDefaultInternalNodesWithDifferentKeyNodesThenNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultInternalNode(
                [1, 5],
                [
                    'b' => new DefaultLeafNode(1),
                    'curry' => new LeafNodeFromResult(new Found([], 1)),
                ]
            ))
                ->equals(
                    new DefaultInternalNode(
                        [1, 5],
                        [
                            'b' => new DefaultLeafNode(1),
                            'curry' => new LeafNodeFromResult(new Found([], 2)),
                        ]
                    )
                )
        );
    }

    private function internalNode(): DefaultInternalNode
    {
        return
            new DefaultInternalNode(
                [1, 5],
                [
                    'b' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 1)
                        ),
                    'curry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            );
    }

    private function assertWhenParamsMethodIsCalledThenAnExceptionWasThrown(Result $result): void
    {
        try {
            $result->values();
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('An exception should have been thrown');
    }

    private function assertWhenClosureMethodIsCalledThenAnExceptionWasThrown(Result $result): void
    {
        try {
            $result->nodeId();
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('An exception should have been thrown');
    }
}