<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeFromResult;
use WrongAboutEverything\RadixTree\ReadModel\Result\Found;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class InternalNodeWithPlaceholderPrefixAndLeafNodeTest extends TestCase
{
    public function testFoundResultWhenThereAreSymbolsAfterPrefix()
    {
        $result =
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                        'barry' =>
                            new LeafNodeFromResult(
                                new Found(['barry1', 'barry2'], 2)
                            ),
                    ]
                ),
                new DefaultLeafNode(3)
            ))
                ->result('123/restaurant/74');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['123'], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    public function testFoundResultWhenThereAreNoMoreSymbolsAfterPrefix()
    {
        $result =
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [5],
                    [
                        'barry' =>
                            new LeafNodeFromResult(
                                new Found(['barry1', 'barry2'], 1)
                            ),
                    ]
                ),
                new DefaultLeafNode(2)
            ))
                ->result('74');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['74'], $result->values());
        $this->assertEquals(2, $result->nodeId());
    }

    public function testNonFoundResult()
    {
        $result =
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                        'barry' =>
                            new LeafNodeFromResult(
                                new Found(['barry1', 'barry2'], 2)
                            ),
                    ]
                ),
                new DefaultLeafNode(3)
            ))
                ->result('123/restaurant/745');

        $this->assertFalse($result->isFound());
        $this->assertWhenParamsMethodIsCalledThenAnExceptionWasThrown($result);
        $this->assertWhenGetNodeIdThenAnExceptionWasThrown($result);
    }

    public function testWhenNodesAreCreatedFromDifferentClassesThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                        'barry' =>
                            new LeafNodeFromResult(
                                new Found(['barry1', 'barry2'], 2)
                            ),
                    ]
                ),
                new DefaultLeafNode(3)
            ))
                ->equals(new DefaultLeafNode(3))
        );
    }

    public function testWhenKeyLengthsAreNotEqualThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                    ]
                ),
                new DefaultLeafNode(4)
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                        new DefaultInternalNode(
                            [14, 6],
                            [
                                '/restaurant/74' => new DefaultLeafNode(1),
                            ]
                        ),
                        new DefaultLeafNode(4)
                    )
                )
        );
    }

    public function testWhenKeyNodesAreNotEqualThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                    ]
                ),
                new DefaultLeafNode(4)
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                        new DefaultInternalNode(
                            [14, 5],
                            [
                                '/restaurant/74' => new DefaultLeafNode(2),
                            ]
                        ),
                        new DefaultLeafNode(4)
                    )
                )
        );
    }

    public function testWhenLeafNodesAreNotEqualThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                    ]
                ),
                new DefaultLeafNode(4)
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                        new DefaultInternalNode(
                            [14, 5],
                            [
                                '/restaurant/74' => new DefaultLeafNode(1),
                            ]
                        ),
                        new DefaultLeafNode(5)
                    )
                )
        );
    }

    public function testWhenKeyLengthsAndKeyNodesAndLeafNodesAreEqualThenTwoNodesAreEqual()
    {
        $this->assertTrue(
            (new InternalNodeWithPlaceholderPrefixAndLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                    ]
                ),
                new DefaultLeafNode(4)
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                        new DefaultInternalNode(
                            [14, 5],
                            [
                                '/restaurant/74' => new DefaultLeafNode(1),
                            ]
                        ),
                        new DefaultLeafNode(4)
                    )
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

    private function assertWhenGetNodeIdThenAnExceptionWasThrown(Result $result): void
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