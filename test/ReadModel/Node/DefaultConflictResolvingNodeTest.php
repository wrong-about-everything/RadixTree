<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeFromResult;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder;
use WrongAboutEverything\RadixTree\ReadModel\Result\NonFound;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class DefaultConflictResolvingNodeTest extends TestCase
{
    public function testFoundResult()
    {
        $result =
            (new DefaultConflictResolvingNode(
                new DefaultInternalNode(
                    [5],
                    [
                        'belov' => new DefaultLeafNode(6),
                    ]
                ),
                new LeafNodeWithTerminatingPlaceholder(0)
            ))
                ->result('belov');

        $this->assertTrue($result->isFound());
        $this->assertEquals([], $result->values());
    }

    public function testNonFoundResult()
    {
        $result =
            (new DefaultConflictResolvingNode(
                new LeafNodeFromResult(
                    new NonFound()
                ),
                new LeafNodeFromResult(
                    new NonFound()
                )
            ))
                ->result('sdfg');

        $this->assertFalse($result->isFound());
        $this->assertWhenParamsMethodIsCalledThenAnExceptionWasThrown($result);
        $this->assertWhenClosureMethodIsCalledThenAnExceptionWasThrown($result);
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