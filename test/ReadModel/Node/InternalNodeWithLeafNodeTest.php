<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeFromResult;
use WrongAboutEverything\RadixTree\ReadModel\Result\Found;

class InternalNodeWithLeafNodeTest extends TestCase
{
    public function testWhenKeyIsEmptyThenLeafNodeResultIsReturned()
    {
        $result =
            (new DefaultInternalNodeWithLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(1),
                        'barry' => new DefaultLeafNode(2),
                    ]
                ),
                new DefaultLeafNode(3)
            ))
                ->result('');

        $this->assertTrue($result->isFound());
        $this->assertEquals([], $result->values());
        $this->assertEquals(3, $result->nodeId());
    }

    public function testWhenKeyIsNonEmptyThenInternalNodeResultIsReturned()
    {
        $result =
            (new DefaultInternalNodeWithLeafNode(
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
                ->result('/restaurant/74');

        $this->assertTrue($result->isFound());
        $this->assertEquals([], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    public function testWhenTwoNodesAreCreatedFromDifferentClassesThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultInternalNodeWithLeafNode(
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
                ->equals(new DefaultLeafNode(2))
        );
    }

    public function testWhenInternalNodesAreEqualButLeafNodesAreNotThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultInternalNodeWithLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(2)
                    ]
                ),
                new DefaultLeafNode(1)
            ))
                ->equals(
                    new DefaultInternalNodeWithLeafNode(
                        new DefaultInternalNode(
                            [14, 5],
                            [
                                '/restaurant/74' => new DefaultLeafNode(2)
                            ]
                        ),
                        new DefaultLeafNode(2)
                    )
                )
        );
    }

    public function testWhenLeafNodesAreEqualButInternalNodesAreNotThenTwoNodesAreNotEqual()
    {
        $this->assertFalse(
            (new DefaultInternalNodeWithLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/75' => new DefaultLeafNode(2)
                    ]
                ),
                new DefaultLeafNode(2)
            ))
                ->equals(
                    new DefaultInternalNodeWithLeafNode(
                        new DefaultInternalNode(
                            [14, 5],
                            [
                                '/restaurant/74' => new DefaultLeafNode(2)
                            ]
                        ),
                        new DefaultLeafNode(2)
                    )
                )
        );
    }

    public function testWhenLeafNodesAreEqualAndInternalNodesAreEqualThenTwoNodesAreEqual()
    {
        $this->assertTrue(
            (new DefaultInternalNodeWithLeafNode(
                new DefaultInternalNode(
                    [14, 5],
                    [
                        '/restaurant/74' => new DefaultLeafNode(2)
                    ]
                ),
                new DefaultLeafNode(2)
            ))
                ->equals(
                    new DefaultInternalNodeWithLeafNode(
                        new DefaultInternalNode(
                            [14, 5],
                            [
                                '/restaurant/74' => new DefaultLeafNode(2)
                            ]
                        ),
                        new DefaultLeafNode(2)
                    )
                )
        );
    }
}