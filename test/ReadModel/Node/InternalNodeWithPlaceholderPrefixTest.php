<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\ReadModel\Node;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeFromResult;
use WrongAboutEverything\RadixTree\ReadModel\Result\Found;
use WrongAboutEverything\RadixTree\ReadModel\Result\Result;

class InternalNodeWithPlaceholderPrefixTest extends TestCase
{
    public function testFoundResultWhenThereAreSymbolsAfterPrefix()
    {
        $result =
            (new InternalNodeWithPlaceholderPrefix(
                [14, 5],
                [
                    '/restaurant/74' => new DefaultLeafNode(1),
                    'barry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            ))
                ->result('123/restaurant/74');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['123'], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    public function testFoundResultWhenThereAreNoMoreSymbolsAfterPrefix()
    {
        $result =
            (new InternalNodeWithPlaceholderPrefix(
                [0, 5],
                [
                    '' => new DefaultLeafNode(1),
                    'barry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            ))
                ->result('74');

        $this->assertTrue($result->isFound());
        $this->assertEquals(['74'], $result->values());
        $this->assertEquals(1, $result->nodeId());
    }

    public function testNonFoundResult()
    {
        $result =
            (new InternalNodeWithPlaceholderPrefix(
                [14, 5],
                [
                    '/restaurant/74' => new DefaultLeafNode(1),
                    'barry' =>
                        new LeafNodeFromResult(
                            new Found(['barry1', 'barry2'], 2)
                        ),
                ]
            ))
                ->result('123/restaurant/745');

        $this->assertFalse($result->isFound());
        $this->assertWhenGettingPlaceholderValuesThenAnExceptionWasThrown($result);
        $this->assertWhenGettingNodeIdThenAnExceptionWasThrown($result);
    }

    public function testWhenNodesAreCreatedFromDifferentClassesThenTheyAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefix(
                [14, 5],
                [
                    '/restaurant/74' => new DefaultLeafNode(3),
                ]
            ))
                ->equals(new DefaultLeafNode(2))
        );
    }

    public function testWhenKeyLengthsAreNotEqualThenNodesAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefix(
                [14, 5],
                [
                    '/restaurant/74' => new DefaultLeafNode(1),
                ]
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefix(
                        [14, 6],
                        [
                            '/restaurant/74' => new DefaultLeafNode(1),
                        ]
                    )
                )
        );
    }

    public function testWhenKeyToNodesAreNotEqualThenNodesAreNotEqual()
    {
        $this->assertFalse(
            (new InternalNodeWithPlaceholderPrefix(
                [14, 5],
                [
                    '/restaurant/74' => new DefaultLeafNode(1),
                ]
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefix(
                        [14, 5],
                        [
                            '/restaurant/74' => new DefaultLeafNode(2),
                        ]
                    )
                )
        );
    }

    public function testWhenKeyLengthsAreEqualAndKeyToNodesAreEqualThenNodesAreEqual()
    {
        $this->assertTrue(
            (new InternalNodeWithPlaceholderPrefix(
                [14, 5],
                [
                    '/restaurant/74' => new DefaultLeafNode(1),
                ]
            ))
                ->equals(
                    new InternalNodeWithPlaceholderPrefix(
                        [14, 5],
                        [
                            '/restaurant/74' => new DefaultLeafNode(1),
                        ]
                    )
                )
        );
    }

    private function assertWhenGettingPlaceholderValuesThenAnExceptionWasThrown(Result $result): void
    {
        try {
            $result->values();
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('An exception should have been thrown');
    }

    private function assertWhenGettingNodeIdThenAnExceptionWasThrown(Result $result): void
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