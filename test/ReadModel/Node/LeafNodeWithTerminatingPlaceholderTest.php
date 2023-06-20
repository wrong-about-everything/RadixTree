<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class LeafNodeWithTerminatingPlaceholderTest extends TestCase
{
    public function testFoundResult()
    {
        $result = (new LeafNodeWithTerminatingPlaceholder(1))->result('dfsdf');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['dfsdf'], $result->values());
    }

    public function testNonFoundResult()
    {
        $result = (new LeafNodeWithTerminatingPlaceholder(3))->result('74/catalogue_item_id/128');

        $this->assertFalse($result->isFound());
        $this->assertWhenParamsMethodIsCalledThenAnExceptionWasThrown($result);
        $this->assertWhenClosureMethodIsCalledThenAnExceptionWasThrown($result);
    }

    public function testGivenTwoLeafNodesWithTheSameIdsThenTwoLeafNodesAreEqual()
    {
        $this->assertTrue(
            (new LeafNodeWithTerminatingPlaceholder(3))
                ->equals(
                    new LeafNodeWithTerminatingPlaceholder(3)
                )
        );
    }

    public function testGivenTwoLeafNodesWithDifferentIdsThenTwoLeafNodesAreNotEqual()
    {
        $this->assertFalse(
            (new LeafNodeWithTerminatingPlaceholder(1))
                ->equals(
                    new LeafNodeWithTerminatingPlaceholder(2)
                )
        );
    }

    public function testGivenOneLeafNodeAndOneInternalNodeThenTheyAreNotEqual()
    {
        $this->assertFalse(
            (new LeafNodeWithTerminatingPlaceholder(3))
                ->equals(
                    new DefaultInternalNode([], [])
                )
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