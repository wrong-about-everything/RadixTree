<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class DefaultLeafNodeTest extends TestCase
{
    public function testFoundResult()
    {
        $result = (new DefaultLeafNode($this->index()))->result('');

        $this->assertTrue($result->isFound());
        $this->assertEquals([], $result->values());
        $this->assertEquals(
            $this->index(),
            $result->nodeId()
        );
    }

    public function testNonFoundResult()
    {
        $result = (new DefaultLeafNode(5))->result('aasdadsdgfg');

        $this->assertFalse($result->isFound());
        $this->assertWhenParamsMethodIsCalledThenAnExceptionWasThrown($result);
        $this->assertWhenClosureMethodIsCalledThenAnExceptionWasThrown($result);
    }

    public function testGivenTwoLeafNodesWithTheSameClosuresThenTwoLeafNodesAreEqual()
    {
        $this->assertTrue(
            (new DefaultLeafNode(
                $this->index()
            ))
                ->equals(
                    new DefaultLeafNode(
                        $this->index()
                    )
                )
        );
    }

    public function testGivenTwoLeafNodesWithDifferentClosuresThenTwoLeafNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultLeafNode(4))
                ->equals(
                    new DefaultLeafNode(5)
                )
        );
    }

    public function testGivenOneLeafNodeAndOneInternalNodeThenTheyAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultLeafNode(
                $this->index()
            ))
                ->equals(
                    new DefaultInternalNode([], [])
                )
        );
    }

    private function index(): int
    {
        return 4;
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