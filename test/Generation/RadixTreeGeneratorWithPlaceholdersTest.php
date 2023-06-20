<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\Generation;

use Exception;
use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\Generation\DataItem;
use WrongAboutEverything\RadixTree\Generation\RadixTreeGenerator;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder;
use WrongAboutEverything\RadixTree\ReadModel\Node\Permutations;
use WrongAboutEverything\RadixTree\ReadModel\Node\RadixTreeNode;
use WrongAboutEverything\RadixTree\Representation\RadixTreeMemoryDump;

class RadixTreeGeneratorWithPlaceholdersTest extends TestCase
{
    public function testWhenDuplicatedKeyIsAddedThenExceptionIsThrown()
    {
        try {
            (new RadixTreeGenerator(
                [
                    $this->dataItem('/vasilevsky/:lisevsky/vasya/:fedya', 1),
                    $this->dataItem('/vasilevsky/:fedya/vasya/:lisevsky', 2),
                ]
            ))
                ->value();
        } catch (Exception $e) {
            $this->assertTrue(true);
            return;
        }

        $this->fail('An exception should have been thrown');
    }

    /**
     * @dataProvider dataItemPermutations
     * @group slow
     */
    public function testDataItemPermutations(array $dataItemPermutations, RadixTreeNode $expectedTree)
    {
        $this->doTestDataItems($dataItemPermutations, $expectedTree);
    }

    static public function dataItemPermutations()
    {
        ini_set('memory_limit', '512M');
        return [
            [
                (new Permutations(self::firstDataItemSet()))->value(), self::radixTreeForFirstDataItemSet()
            ],
        ];
    }

    /**
     * @dataProvider randomDataItems
     * @group slow
     */
    public function testRandomDataItems(array $dataItemPermutations, RadixTreeNode $expectedTree)
    {
        $this->doTestDataItems($dataItemPermutations, $expectedTree);
    }

    static public function randomDataItems()
    {
        ini_set('memory_limit', '512M');
        return [
            [
                array_map(
                    function () {
                        $randomDataItemSet = self::randomDataItemSet();
                        shuffle($randomDataItemSet);
                        return $randomDataItemSet;
                    },
                    range(0, 10000)
                ),
                self::radixTreeForRandomDataItemSet()
            ],
        ];
    }

    private function doTestDataItems(array $dataItemPermutations, RadixTreeNode $expectedTree)
    {
        foreach ($dataItemPermutations as $dataItems) {
            try {
                $radixTree = (new RadixTreeGenerator($dataItems))->value();
            } catch (Exception $e) {
                $this->fail(
                    sprintf(
                        'Current data item permutation results in an exception: %s, %s. You can print those dataItems with $this->printDataItems($dataItems) method.',
                        $e->getMessage(),
                        $e->getTraceAsString()
                    )
                );
            }
            if (!$radixTree->equals($expectedTree)) {
                $this->fail(
                    sprintf(
                        'Radix tree built with current data item permutation is incorrect. You can print those dataItems with $this->printDataItems($dataItems) method. To see the resulting tree, use %s',
                        RadixTreeMemoryDump::class
                    )
                );
            }
        }

        $this->lookUpKeys($dataItems, $radixTree);
        $this->assertTrue(true);
    }

    /**
     * @param DataItem[] $dataItems
     * @param RadixTreeNode $radixTreeNode
     * @return void
     */
    private function lookUpKeys(array $dataItems, RadixTreeNode $radixTreeNode)
    {
        foreach ($dataItems as $dataItem) {
            $queryStringAndGeneratedPlaceholders = $this->queryStringAndGeneratedPlaceholders($dataItem->key());
            $result = $radixTreeNode->result($queryStringAndGeneratedPlaceholders[0]);
            $this->assertTrue($result->isFound());
            $this->assertEquals($queryStringAndGeneratedPlaceholders[1], $result->values());
        }
    }

    private function queryStringAndGeneratedPlaceholders(string $key): array
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $generatedPlaceholders = [];
        $queryParts = array_filter(explode('/', $key));
        foreach ($queryParts as &$part) {
            if ($part[0] === ':') {
                $randomValue = substr(str_shuffle($characters), 0, mt_rand(1, 10));
                $generatedPlaceholders[] = $randomValue;
                $part = $randomValue;
            }
        }

        return ['/' . implode('/', $queryParts) . ($key[strlen($key) - 1] === '/' ? '/' : ''), $generatedPlaceholders];
    }

    private function printDataItems(array $dataItems): void
    {
        array_walk(
            $dataItems,
            function (DataItem $dataItem) {
                var_dump($dataItem->key());
            }
        );
    }

    static private function firstDataItemSet(): array
    {
        return [
            self::dataItem('/vasilevich/:makarevich/:vasily/belov', 0),
            self::dataItem('/vasilev/:anton/vasily/:belov', 1),
            self::dataItem('/vasilev/:anton/anton/:petrovich/anton/:vasilyev', 2),
            self::dataItem('/vasile/:anton/vakhlakov', 3),
            self::dataItem('/vasilevs/:antons/tabakovs/:matskyavichus', 4),
            self::dataItem('/vasile/:anton', 5),
            self::dataItem('/vasile', 6),
        ];
    }

    static private function radixTreeForFirstDataItemSet(): RadixTreeNode
    {
        return
            new DefaultInternalNode(
                [7],
                [
                    '/vasile' =>
                        new DefaultInternalNodeWithLeafNode(
                            new DefaultInternalNode(
                                [1],
                                [
                                    'v' =>
                                        new DefaultInternalNode(
                                            [1, 2, 4],
                                            [
                                                'ich/' =>
                                                    new InternalNodeWithPlaceholderPrefix(
                                                        [1],
                                                        [
                                                            '/' =>
                                                                new InternalNodeWithPlaceholderPrefix(
                                                                    [6],
                                                                    [
                                                                        '/belov' => new DefaultLeafNode(0),

                                                                    ]
                                                                ),
                                                        ]
                                                    ),
                                                '/' =>
                                                    new InternalNodeWithPlaceholderPrefix(
                                                        [1],
                                                        [
                                                            '/' =>
                                                                new DefaultInternalNode(
                                                                    [6, 7],
                                                                    [
                                                                        'vasily/' => new LeafNodeWithTerminatingPlaceholder(1),
                                                                        'anton/' =>
                                                                            new InternalNodeWithPlaceholderPrefix(
                                                                                [7],
                                                                                [
                                                                                    '/anton/' => new LeafNodeWithTerminatingPlaceholder(2),
                                                                                ]
                                                                            ),
                                                                    ]
                                                                )
                                                        ]
                                                    ),
                                                's/' =>
                                                    new InternalNodeWithPlaceholderPrefix(
                                                        [10],
                                                        [
                                                            '/tabakovs/' => new LeafNodeWithTerminatingPlaceholder(4),

                                                        ]
                                                    ),
                                            ]
                                        ),
                                    '/' =>
                                        new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                            new DefaultInternalNode(
                                                [10],
                                                [
                                                    '/vakhlakov' => new DefaultLeafNode(3),
                                                ]
                                            ),
                                            new DefaultLeafNode(5)
                                        ),
                                ]
                            ),
                            new DefaultLeafNode(6)
                        ),
                ]
            );
    }

    static private function randomDataItemSet(): array
    {
        return [
            self::dataItem('/vasilevich/:makarevich/:vasily/belov', 0),
            self::dataItem('/vasilev/:anton/vasily/:belov', 1),
            self::dataItem('/vasilev/:anton/anton/:petrovich/anton/:vasilyev', 2),
            self::dataItem('/vasile/:anton/:vakhlakov', 3),
            self::dataItem('/vasile/:anton', 5),
            self::dataItem('/vasile', 6),
            self::dataItem('/vasilevs', 7),
            self::dataItem('/vasilevs/antons/:tabakovs', 8),
            self::dataItem('/vasilevs/antons/:tabakovs/matskyavichus', 9),
        ];
    }

    static private function radixTreeForRandomDataItemSet(): RadixTreeNode
    {
        return
            new DefaultInternalNode(
                [7],
                [
                    '/vasile' =>
                        new DefaultInternalNodeWithLeafNode(

                            new DefaultInternalNode(
                                [1],
                                [
                                    '/' =>
                                        new InternalNodeWithPlaceholderPrefixAndLeafNode(

                                            new DefaultInternalNode(
                                                [1],
                                                [
                                                    '/' => new LeafNodeWithTerminatingPlaceholder(3),

                                                ]
                                            ),
                                            new DefaultLeafNode(5)
                                        ),
                                    'v' =>
                                        new DefaultInternalNode(
                                            [1, 4],
                                            [
                                                'ich/' =>
                                                    new InternalNodeWithPlaceholderPrefix(
                                                        [1],
                                                        [
                                                            '/' =>
                                                                new InternalNodeWithPlaceholderPrefix(
                                                                    [6],
                                                                    [
                                                                        '/belov' => new DefaultLeafNode(0),

                                                                    ]
                                                                ),

                                                        ]
                                                    ),
                                                '/' =>
                                                    new InternalNodeWithPlaceholderPrefix(
                                                        [1],
                                                        [
                                                            '/' =>
                                                                new DefaultInternalNode(
                                                                    [6, 7],
                                                                    [
                                                                        'vasily/' => new LeafNodeWithTerminatingPlaceholder(1),
                                                                        'anton/' =>
                                                                            new InternalNodeWithPlaceholderPrefix(
                                                                                [7],
                                                                                [
                                                                                    '/anton/' => new LeafNodeWithTerminatingPlaceholder(2),

                                                                                ]
                                                                            ),

                                                                    ]
                                                                ),

                                                        ]
                                                    ),
                                                's' =>
                                                    new DefaultInternalNodeWithLeafNode(

                                                        new DefaultInternalNode(
                                                            [8],
                                                            [
                                                                '/antons/' =>
                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(

                                                                        new DefaultInternalNode(
                                                                            [14],
                                                                            [
                                                                                '/matskyavichus' => new DefaultLeafNode(9),

                                                                            ]
                                                                        ),
                                                                        new DefaultLeafNode(8)
                                                                    ),

                                                            ]
                                                        ),
                                                        new DefaultLeafNode(7)
                                                    ),

                                            ]
                                        ),

                                ]
                            ),
                            new DefaultLeafNode(6)
                        ),

                ]
            );
    }

    static private function dataItem(string $path, int $index): DataItem
    {
        return new DataItem($path, $index);
    }
}
