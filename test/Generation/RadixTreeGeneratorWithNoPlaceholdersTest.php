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
use WrongAboutEverything\RadixTree\ReadModel\Node\Permutations;
use WrongAboutEverything\RadixTree\ReadModel\Node\RadixTreeNode;
use WrongAboutEverything\RadixTree\Representation\RadixTreeMemoryDump;

class RadixTreeGeneratorWithNoPlaceholdersTest extends TestCase
{
    /**
     * @dataProvider dataItemPermutations
     * @group slow
     */
    public function testDataItemPermutations(array $dataItemPermutations, RadixTreeNode $expectedTree)
    {
        $this->doTestDataItems($dataItemPermutations, $expectedTree);
    }

    public function testVasya()
    {
        $this->doTestDataItems(
            [
                [
                    self::dataItem('/vas/hey', 1),
                    self::dataItem('/vas/heyday', 2),
                ]
            ],
            new DefaultInternalNode(
                [8],
                [
                    '/vas/hey' =>
                        new DefaultInternalNodeWithLeafNode(
                            new DefaultInternalNode(
                                [3],
                                [
                                    'day' => new DefaultLeafNode(2)
                                ]
                            ),
                            new DefaultLeafNode(1)
                        )
                ]
            )
        );
    }

    static public function dataItemPermutations()
    {
        ini_set('memory_limit', '512M');
        return [
            [
                (new Permutations(self::firstDataItemSet()))->value(), self::radixTreeForFirstDataItemSet()
            ],
            [
                (new Permutations(self::secondDataItemSet()))->value(), self::radixTreeForSecondDataItemSet()
            ],
        ];
    }

    /**
     * @dataProvider randomDataItemsCombinations
     */
    public function testRandomDataItemCombinations(array $dataItemPermutation, RadixTreeNode $expectedTree)
    {
        $this->doTestDataItems($dataItemPermutation, $expectedTree);
    }

    static public function randomDataItemsCombinations()
    {
        $randomCombinations = [];
        $allDataItems = self::allDataItems();
        for ($i = 0; $i <= 1000; $i++) {
            shuffle($allDataItems);
            $randomCombinations[] = $allDataItems;
        }

        return [[$randomCombinations, self::entireTree()]];
    }

    static private function entireTree(): RadixTreeNode
    {
        return
            new DefaultInternalNode(
                [6],
                [
                    '/vasil' =>
                        new DefaultInternalNode(
                            [1, 3],
                            [
                                'e' =>
                                    new DefaultInternalNodeWithLeafNode(
                                        new DefaultInternalNode(
                                            [1],
                                            [
                                                'v' =>
                                                    new DefaultInternalNodeWithLeafNode(
                                                        new DefaultInternalNode(
                                                            [1],
                                                            [
                                                                'i' =>
                                                                    new DefaultInternalNodeWithLeafNode(

                                                                        new DefaultInternalNode(
                                                                            [2],
                                                                            [
                                                                                'ch' => new DefaultLeafNode(2),

                                                                            ]
                                                                        ),
                                                                        new DefaultLeafNode(8)
                                                                    ),
                                                                's' =>
                                                                    new DefaultInternalNodeWithLeafNode(

                                                                        new DefaultInternalNode(
                                                                            [1, 2],
                                                                            [
                                                                                'ky' => new DefaultLeafNode(1),
                                                                                's' => new DefaultLeafNode(10),

                                                                            ]
                                                                        ),
                                                                        new DefaultLeafNode(6)
                                                                    ),
                                                                'v' => new DefaultLeafNode(9),

                                                            ]
                                                        ),
                                                        new DefaultLeafNode(4)
                                                    ),
                                            ]
                                        ),
                                        new DefaultLeafNode(7)
                                    ),
                                'kov' => new DefaultLeafNode(3),
                                'y' => new DefaultLeafNode(0),
                            ]
                        ),
                ]
            );
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
            $result = $radixTreeNode->result($dataItem->key());
            $this->assertTrue($result->isFound());
        }
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
            self::dataItem('/vasilevsky', 1),
            self::dataItem('/vasilevs', 6),
            self::dataItem('/vasilev', 4),
            self::dataItem('/vasilevich', 2),
            self::dataItem('/vasile', 7),
            self::dataItem('/vasilevi', 8),
            self::dataItem('/vasilevv', 9),
            self::dataItem('/vasilevss', 10),
        ];
    }

    static private function secondDataItemSet(): array
    {
        return [
            self::dataItem('/vasilevsky', 1),
            self::dataItem('/vasilevs', 6),
            self::dataItem('/vasily', 0),
            self::dataItem('/vasilev', 4),
            self::dataItem('/vasilkov', 3),
            self::dataItem('/vasilevich', 2),
            self::dataItem('/vasile', 7),
        ];
    }

    static private function allDataItems()
    {
        return [
            self::dataItem('/vasilevs', 6),
            self::dataItem('/vasilevsky', 1),
            self::dataItem('/vasily', 0),
            self::dataItem('/vasilev', 4),
            self::dataItem('/vasilkov', 3),
            self::dataItem('/vasilevich', 2),
            self::dataItem('/vasile', 7),
            self::dataItem('/vasilevi', 8),
            self::dataItem('/vasilevv', 9),
            self::dataItem('/vasilevss', 10),
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
                                        new DefaultInternalNodeWithLeafNode(

                                            new DefaultInternalNode(
                                                [1],
                                                [
                                                    's' =>
                                                        new DefaultInternalNodeWithLeafNode(

                                                            new DefaultInternalNode(
                                                                [1, 2],
                                                                [
                                                                    'ky' => new DefaultLeafNode(1),
                                                                    's' => new DefaultLeafNode(10),

                                                                ]
                                                            ),
                                                            new DefaultLeafNode(6)
                                                        ),
                                                    'i' =>
                                                        new DefaultInternalNodeWithLeafNode(

                                                            new DefaultInternalNode(
                                                                [2],
                                                                [
                                                                    'ch' => new DefaultLeafNode(2),

                                                                ]
                                                            ),
                                                            new DefaultLeafNode(8)
                                                        ),
                                                    'v' => new DefaultLeafNode(9),

                                                ]
                                            ),
                                            new DefaultLeafNode(4)
                                        ),

                                ]
                            ),
                            new DefaultLeafNode(7)
                        ),

                ]
            );
    }

    static private function radixTreeForSecondDataItemSet(): RadixTreeNode
    {
        return
            new DefaultInternalNode(
                [6],
                [
                    '/vasil' =>
                        new DefaultInternalNode(
                            [1, 3],
                            [
                                'y' => new DefaultLeafNode(0),
                                'kov' => new DefaultLeafNode(3),
                                'e' =>
                                    new DefaultInternalNodeWithLeafNode(
                                        new DefaultInternalNode(
                                            [1],
                                            [
                                                'v' =>
                                                    new DefaultInternalNodeWithLeafNode(
                                                        new DefaultInternalNode(
                                                            [1, 3],
                                                            [
                                                                's' =>
                                                                    new DefaultInternalNodeWithLeafNode(

                                                                        new DefaultInternalNode(
                                                                            [2],
                                                                            [
                                                                                'ky' => new DefaultLeafNode(1),

                                                                            ]
                                                                        ),
                                                                        new DefaultLeafNode(6)
                                                                    ),
                                                                'ich' => new DefaultLeafNode(2),
                                                            ]
                                                        ),
                                                        new DefaultLeafNode(4)
                                                    ),
                                            ]
                                        ),
                                        new DefaultLeafNode(7)
                                    ),
                            ]
                        ),
                ]
            );
    }

    static private function dataItem(string $path, int $index): DataItem
    {
        return new DataItem($path, $index);
    }
}
