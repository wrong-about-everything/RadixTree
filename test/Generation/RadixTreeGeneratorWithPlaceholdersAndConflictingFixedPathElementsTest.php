<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\Generation;

use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\Generation\DataItem;
use WrongAboutEverything\RadixTree\Generation\RadixTreeGenerator;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder;
use WrongAboutEverything\RadixTree\ReadModel\Node\Permutations;
use WrongAboutEverything\RadixTree\ReadModel\Node\RadixTreeNode;
use WrongAboutEverything\RadixTree\Representation\RadixTreeMemoryDump;
use Throwable;

class RadixTreeGeneratorWithPlaceholdersAndConflictingFixedPathElementsTest extends TestCase
{
    /**
     * @dataProvider fastDataItems
     */
    public function testFast(Permutations $permutations, RadixTreeNode $expected)
    {
        $this->doTestDataItems($permutations->value(), $expected);
    }

    static public function fastDataItems()
    {
        return [
            [
                new Permutations([self::dataItem('/vasilev/makarev', 1), self::dataItem('/vasilev/:belov', 0)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [7],
                                    ['makarev' => new DefaultLeafNode(1)]
                                ),
                                new LeafNodeWithTerminatingPlaceholder(0)
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov/vasya', 1), self::dataItem('/vasilev/sayva/lebov', 0)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [11],
                                    ['sayva/lebov' => new DefaultLeafNode(0)]
                                ),
                                new InternalNodeWithPlaceholderPrefix(
                                    [6],
                                    [
                                        '/vasya' => new DefaultLeafNode(1)
                                    ]
                                )
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov/vasya', 0), self::dataItem('/vasilev/sayva/:lebov', 1)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [6],
                                    [
                                        'sayva/' => new LeafNodeWithTerminatingPlaceholder(1)
                                    ]
                                ),
                                new InternalNodeWithPlaceholderPrefix(
                                    [6],
                                    [
                                        '/vasya' => new DefaultLeafNode(0)
                                    ]
                                )
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov/vasya', 0), self::dataItem('/vasilev/:sayva/lebov', 1)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new InternalNodeWithPlaceholderPrefix(
                                [1],
                                [
                                    '/' =>
                                        new DefaultInternalNode(
                                            [5],
                                            [
                                                'vasya' => new DefaultLeafNode(0),
                                                'lebov' => new DefaultLeafNode(1),
                                            ]
                                        )
                                ]
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov/:vasya', 0), self::dataItem('/vasilev/sayva/lebov', 1)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [11],
                                    [
                                        'sayva/lebov' => new DefaultLeafNode(1)
                                    ]
                                ),
                                new InternalNodeWithPlaceholderPrefix(
                                    [1],
                                    [
                                        '/' => new LeafNodeWithTerminatingPlaceholder(0)
                                    ]
                                )
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov/:vasya', 0), self::dataItem('/vasilev/sayva/:lebov', 1)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [6],
                                    [
                                        'sayva/' => new LeafNodeWithTerminatingPlaceholder(1)
                                    ]
                                ),
                                new InternalNodeWithPlaceholderPrefix(
                                    [1],
                                    [
                                        '/' => new LeafNodeWithTerminatingPlaceholder(0)
                                    ]
                                )
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov/vasya', 0), self::dataItem('/vasilev/:sayva/:lebov', 1)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new InternalNodeWithPlaceholderPrefix(
                                [1],
                                [
                                    '/' =>
                                        new DefaultConflictResolvingNode(
                                            new DefaultInternalNode(
                                                [5],
                                                [
                                                    'vasya' => new DefaultLeafNode(0)
                                                ]
                                            ),
                                            new LeafNodeWithTerminatingPlaceholder(1)
                                        )
                                ]
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:belov', 0), self::dataItem('/vasilev/sayva/lebov', 1)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [11],
                                    [
                                        'sayva/lebov' => new DefaultLeafNode(1)
                                    ]
                                ),
                                new LeafNodeWithTerminatingPlaceholder(0)
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/sayva', 1), self::dataItem('/vasilev/:belov/vasya', 0)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [5],
                                    [
                                        'sayva' => new DefaultLeafNode(1)
                                    ]
                                ),
                                new InternalNodeWithPlaceholderPrefix(
                                    [6],
                                    [
                                        '/vasya' => new DefaultLeafNode(0)
                                    ]
                                )
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/sayva/:lebov', 1), self::dataItem('/vasilev/:belov', 0)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [6],
                                    [
                                        'sayva/' => new LeafNodeWithTerminatingPlaceholder(1)
                                    ]
                                ),
                                new LeafNodeWithTerminatingPlaceholder(0)
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/:sayva/lebov', 1), self::dataItem('/vasilev/:belov', 0)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                new DefaultInternalNode(
                                    [6],
                                    [
                                        '/lebov' => new DefaultLeafNode(1)
                                    ]
                                ),
                                new DefaultLeafNode(0)
                            )
                    ]
                )
            ],
            [
                new Permutations([self::dataItem('/vasilev/sayva', 1), self::dataItem('/vasilev/:belov/:vasya', 0)]),
                new DefaultInternalNode(
                    [9],
                    [
                        '/vasilev/' =>
                            new DefaultConflictResolvingNode(
                                new DefaultInternalNode(
                                    [5],
                                    [
                                        'sayva' => new DefaultLeafNode(1)
                                    ]
                                ),
                                new InternalNodeWithPlaceholderPrefix(
                                    [1],
                                    [
                                        '/' => new LeafNodeWithTerminatingPlaceholder(0)
                                    ]
                                )
                            )
                    ]
                )
            ],
        ];
    }

    /**
     * @dataProvider slowerDataItems
     * @group slow
     */
    public function testSlowerDataItems(Permutations $permutations, RadixTreeNode $expected)
    {
        $this->doTestDataItems($permutations->value(), $expected);
    }

    static public function slowerDataItems()
    {
        ini_set('memory_limit', '512M');
        return [
            [
                new Permutations(self::dataItemsForPermutations()),
                new DefaultInternalNode(
                    [6],
                    [
                        '/vasil' =>
                            new DefaultInternalNode(
                                [1, 2],
                                [
                                    'y/' =>
                                        new DefaultConflictResolvingNode(
                                            new DefaultInternalNode(
                                                [5],
                                                [
                                                    'belov' => new DefaultLeafNode(6),
                                                ]
                                            ),
                                            new LeafNodeWithTerminatingPlaceholder(0)
                                        ),
                                    'e' =>
                                        new DefaultInternalNode(
                                            [1],
                                            [
                                                'v' =>
                                                    new DefaultInternalNode(
                                                        [1, 2],
                                                        [
                                                            '/' =>
                                                                new DefaultConflictResolvingNode(
                                                                    new DefaultInternalNode(
                                                                        [6],
                                                                        [
                                                                            'baton/' =>
                                                                                new DefaultConflictResolvingNode(
                                                                                    new DefaultInternalNode(
                                                                                        [10],
                                                                                        [
                                                                                            'vakhlakov/' => new LeafNodeWithTerminatingPlaceholder(3),
                                                                                        ]
                                                                                    ),
                                                                                    new InternalNodeWithPlaceholderPrefix(
                                                                                        [10],
                                                                                        [
                                                                                            '/vakhlakov' => new DefaultLeafNode(3),
                                                                                        ]
                                                                                    )
                                                                                ),
                                                                        ]
                                                                    ),
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
                                                                    )
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
                                                '/' => new LeafNodeWithTerminatingPlaceholder(5),
                                            ]
                                        ),
                                ]
                            ),
                    ]
                )
            ],
        ];
    }

    /**
     * @dataProvider slowestDataItems
     * @group slow
     */
    public function testSlowestDataItems(array $dataItemPermutations, RadixTreeNode $expectedTree)
    {
        $this->doTestDataItems($dataItemPermutations, $expectedTree);
    }

    static public function slowestDataItems()
    {
        ini_set('memory_limit', '512M');

        $randomCombinations = [];
        $allDataItems = self::enormousDataItems();
        for ($i = 0; $i <= 10000; $i++) {
            shuffle($allDataItems);
            $randomCombinations[] = $allDataItems;
        }

        return [[$randomCombinations, self::tree()]];
    }

    private function doTestDataItems(array $dataItemPermutations, RadixTreeNode $expectedTree)
    {
        foreach ($dataItemPermutations as $dataItems) {
            try {
                $radixTree = (new RadixTreeGenerator($dataItems))->value();
            } catch (Throwable $e) {
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

        return ['/' . implode('/', $queryParts), $generatedPlaceholders];
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

    static private function dataItemsForPermutations(): array
    {
        return
            [
                self::dataItem('/vasily/:lebov', 0),
                self::dataItem('/vasily/belov', 6),
                self::dataItem('/vasilev/:anton/vasily/:belov', 1),
                self::dataItem('/vasilev/:anton/anton/:petrovich/anton/:vasilyev', 2),
                self::dataItem('/vasilev/baton/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilevs/:antons/tabakovs/:matskyavichus', 4),
                self::dataItem('/vasile/:anton', 5),
            ];
    }

    static private function enormousDataItems(): array
    {
        return
            [
                self::dataItem('/vasilev/baton/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/baton/:vakhlakov/vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/:vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/:baton/vakhlakov/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/:vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov/buldakov', 3),
                self::dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov/:buldakov', 3),
                self::dataItem('/vasilev/makarevi/:vasily/:belov', 6),
                self::dataItem('/vasile/:anton/vakhlakov', 10),
                self::dataItem('/vasily/:lebov', 0),
                self::dataItem('/vasilev/makarev', 3),
                self::dataItem('/vasilev/makarev/:vasily/:belov', 7),
                self::dataItem('/vasilev/:anton/vasily/:belov', 1),
                self::dataItem('/vasilev/:anton/vasily/belov', 1),
                self::dataItem('/vasilev/:anton/vasily/belov/:htonc', 1),
                self::dataItem('/vasilev/:anton/vasily/:belov/htonc', 1),
                self::dataItem('/vasilev/:anton/vasily/belov/htonc', 1),
                self::dataItem('/vasilev/makarevi', 4),
                self::dataItem('/vasilevs/:antons/tabakovs/:matskyavichus', 4),
                self::dataItem('/vasily/belov', 6),
                self::dataItem('/vasilev/:anton', 8),
                self::dataItem('/vasilev/:anton/anton', 2),
                self::dataItem('/vasilev/:anton/anton/:petrovich', 2),
                self::dataItem('/vasilev/:anton/anton/:petrovich/anton', 2),
                self::dataItem('/vasilev/:anton/anton/:petrovich/anton/:vasilyev', 2),
                self::dataItem('/vasilev/anton/:anton/petrovich/:anton/vasilyev', 2),
                self::dataItem('/vasilev/anton/:anton/petrovich/:anton', 2),
                self::dataItem('/vasilev/anton/:anton/petrovich', 2),
                self::dataItem('/vasilev/anton/:anton', 2),
                self::dataItem('/vasilev/anton', 2),
                self::dataItem('/vasilevich/:makarevich/:vasily/belov', 2),
                self::dataItem('/vasile/antoine/vakhlakov', 12),
                self::dataItem('/vasilev', 5),
                self::dataItem('/vasilev/makarevich', 5),
                self::dataItem('/vasilev/makarevich/vasilev', 5),
                self::dataItem('/vasilev/makarevich/vasilev/makarevich', 5),
                self::dataItem('/vasilev/makarevich/vasilev/makarevich/vasilev', 5),
                self::dataItem('/vasile/:anton', 5),
            ];
    }

    static private function tree(): RadixTreeNode
    {
        return
            new DefaultInternalNode(
                [6],
                [
                    '/vasil' =>
                        new DefaultInternalNode(
                            [1, 2],
                            [
                                'e' =>
                                    new DefaultInternalNode(
                                        [1],
                                        [
                                            '/' =>
                                                new DefaultConflictResolvingNode(

                                                    new DefaultInternalNode(
                                                        [17],
                                                        [
                                                            'antoine/vakhlakov' => new DefaultLeafNode(12),
                                                        ]
                                                    ),

                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                        new DefaultInternalNode(
                                                            [10],
                                                            [
                                                                '/vakhlakov' => new DefaultLeafNode(10),
                                                            ]
                                                        ),
                                                        new DefaultLeafNode(5)
                                                    )
                                                ),
                                            'v' =>
                                                new DefaultInternalNodeWithLeafNode(
                                                    new DefaultInternalNode(
                                                        [1, 2, 4],
                                                        [
                                                            '/' =>
                                                                new DefaultConflictResolvingNode(
                                                                    new DefaultInternalNode(
                                                                        [5, 6, 7],
                                                                        [
                                                                            'baton/' =>
                                                                                new DefaultConflictResolvingNode(
                                                                                    new DefaultInternalNode(
                                                                                        [10],
                                                                                        [
                                                                                            'vakhlakov/' =>
                                                                                                new DefaultConflictResolvingNode(
                                                                                                    new DefaultInternalNode(
                                                                                                        [9],
                                                                                                        [
                                                                                                            'vakhlakov' =>
                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                    new DefaultInternalNode(
                                                                                                                        [1],
                                                                                                                        [
                                                                                                                            '/' =>
                                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                                    new DefaultInternalNode(
                                                                                                                                        [9],
                                                                                                                                        [
                                                                                                                                            'vakhlakov' =>
                                                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                        [1],
                                                                                                                                                        [
                                                                                                                                                            '/' =>
                                                                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                                        [8],
                                                                                                                                                                        [
                                                                                                                                                                            'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                                        ]
                                                                                                                                                                    ),
                                                                                                                                                                    new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                                ),
                                                                                                                                                        ]
                                                                                                                                                    ),
                                                                                                                                                    new DefaultLeafNode(3)
                                                                                                                                                ),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new DefaultLeafNode(3)
                                                                                                                ),
                                                                                                        ]
                                                                                                    ),
                                                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                        new DefaultInternalNode(
                                                                                                            [1],
                                                                                                            [
                                                                                                                '/' =>
                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                        new DefaultInternalNode(
                                                                                                                            [9],
                                                                                                                            [
                                                                                                                                'vakhlakov' =>
                                                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                                                        new DefaultInternalNode(
                                                                                                                                            [1],
                                                                                                                                            [
                                                                                                                                                '/' =>
                                                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                                                        new DefaultInternalNode(
                                                                                                                                                            [8],
                                                                                                                                                            [
                                                                                                                                                                'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                            ]
                                                                                                                                                        ),
                                                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                    ),
                                                                                                                                            ]
                                                                                                                                        ),
                                                                                                                                        new DefaultLeafNode(3)
                                                                                                                                    ),
                                                                                                                            ]
                                                                                                                        ),
                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                    ),
                                                                                                            ]
                                                                                                        ),
                                                                                                        new DefaultLeafNode(3)
                                                                                                    )
                                                                                                ),
                                                                                        ]
                                                                                    ),
                                                                                    new InternalNodeWithPlaceholderPrefix(
                                                                                        [1],
                                                                                        [
                                                                                            '/' =>
                                                                                                new DefaultConflictResolvingNode(
                                                                                                    new DefaultInternalNode(
                                                                                                        [9],
                                                                                                        [
                                                                                                            'vakhlakov' =>
                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                    new DefaultInternalNode(
                                                                                                                        [1],
                                                                                                                        [
                                                                                                                            '/' =>
                                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                                    new DefaultInternalNode(
                                                                                                                                        [9],
                                                                                                                                        [
                                                                                                                                            'vakhlakov' => new DefaultLeafNode(3),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new DefaultLeafNode(3)
                                                                                                                ),
                                                                                                        ]
                                                                                                    ),
                                                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                        new DefaultInternalNode(
                                                                                                            [1],
                                                                                                            [
                                                                                                                '/' =>
                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                        new DefaultInternalNode(
                                                                                                                            [9],
                                                                                                                            [
                                                                                                                                'vakhlakov' =>
                                                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                                                        new DefaultInternalNode(
                                                                                                                                            [1],
                                                                                                                                            [
                                                                                                                                                '/' =>
                                                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                                                        new DefaultInternalNode(
                                                                                                                                                            [8],
                                                                                                                                                            [
                                                                                                                                                                'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                            ]
                                                                                                                                                        ),
                                                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                    ),
                                                                                                                                            ]
                                                                                                                                        ),
                                                                                                                                        new DefaultLeafNode(3)
                                                                                                                                    ),
                                                                                                                            ]
                                                                                                                        ),
                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                    ),
                                                                                                            ]
                                                                                                        ),
                                                                                                        new DefaultLeafNode(3)
                                                                                                    )
                                                                                                ),
                                                                                        ]
                                                                                    )
                                                                                ),
                                                                            'anton' =>
                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                    new DefaultInternalNode(
                                                                                        [1],
                                                                                        [
                                                                                            '/' =>
                                                                                                new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                    new DefaultInternalNode(
                                                                                                        [10],
                                                                                                        [
                                                                                                            '/petrovich' =>
                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                    new DefaultInternalNode(
                                                                                                                        [1],
                                                                                                                        [
                                                                                                                            '/' =>
                                                                                                                                new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                                                    new DefaultInternalNode(
                                                                                                                                        [9],
                                                                                                                                        [
                                                                                                                                            '/vasilyev' => new DefaultLeafNode(2),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new DefaultLeafNode(2)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new DefaultLeafNode(2)
                                                                                                                ),
                                                                                                        ]
                                                                                                    ),
                                                                                                    new DefaultLeafNode(2)
                                                                                                ),
                                                                                        ]
                                                                                    ),
                                                                                    new DefaultLeafNode(2)
                                                                                ),
                                                                            'makarev' =>
                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                    new DefaultInternalNode(
                                                                                        [1],
                                                                                        [
                                                                                            'i' =>
                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                    new DefaultInternalNode(
                                                                                                        [1, 2],
                                                                                                        [
                                                                                                            'ch' =>
                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                    new DefaultInternalNode(
                                                                                                                        [8],
                                                                                                                        [
                                                                                                                            '/vasilev' =>
                                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                                    new DefaultInternalNode(
                                                                                                                                        [11],
                                                                                                                                        [
                                                                                                                                            '/makarevich' =>
                                                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                        [8],
                                                                                                                                                        [
                                                                                                                                                            '/vasilev' => new DefaultLeafNode(5),
                                                                                                                                                        ]
                                                                                                                                                    ),
                                                                                                                                                    new DefaultLeafNode(5)
                                                                                                                                                ),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new DefaultLeafNode(5)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new DefaultLeafNode(5)
                                                                                                                ),
                                                                                                            '/' =>
                                                                                                                new InternalNodeWithPlaceholderPrefix(
                                                                                                                    [1],
                                                                                                                    [
                                                                                                                        '/' => new LeafNodeWithTerminatingPlaceholder(6),
                                                                                                                    ]
                                                                                                                ),
                                                                                                        ]
                                                                                                    ),
                                                                                                    new DefaultLeafNode(4)
                                                                                                ),
                                                                                            '/' =>
                                                                                                new InternalNodeWithPlaceholderPrefix(
                                                                                                    [1],
                                                                                                    [
                                                                                                        '/' => new LeafNodeWithTerminatingPlaceholder(7),
                                                                                                    ]
                                                                                                ),
                                                                                        ]
                                                                                    ),
                                                                                    new DefaultLeafNode(3)
                                                                                ),
                                                                        ]
                                                                    ),
                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                        new DefaultInternalNode(
                                                                            [1],
                                                                            [
                                                                                '/' =>
                                                                                    new DefaultConflictResolvingNode(
                                                                                        new DefaultInternalNode(
                                                                                            [2, 5],
                                                                                            [
                                                                                                'anton' =>
                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                        new DefaultInternalNode(
                                                                                                            [1],
                                                                                                            [
                                                                                                                '/' =>
                                                                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                                        new DefaultInternalNode(
                                                                                                                            [6],
                                                                                                                            [
                                                                                                                                '/anton' =>
                                                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                                                        new DefaultInternalNode(
                                                                                                                                            [1],
                                                                                                                                            [
                                                                                                                                                '/' => new LeafNodeWithTerminatingPlaceholder(2),
                                                                                                                                            ]
                                                                                                                                        ),
                                                                                                                                        new DefaultLeafNode(2)
                                                                                                                                    ),
                                                                                                                            ]
                                                                                                                        ),
                                                                                                                        new DefaultLeafNode(2)
                                                                                                                    ),
                                                                                                            ]
                                                                                                        ),
                                                                                                        new DefaultLeafNode(2)
                                                                                                    ),
                                                                                                'va' =>
                                                                                                    new DefaultInternalNode(
                                                                                                        [5, 8],
                                                                                                        [
                                                                                                            'khlakov/' =>
                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                    new DefaultInternalNode(
                                                                                                                        [9],
                                                                                                                        [
                                                                                                                            'vakhlakov' =>
                                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                                    new DefaultInternalNode(
                                                                                                                                        [1],
                                                                                                                                        [
                                                                                                                                            '/' =>
                                                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                        [9],
                                                                                                                                                        [
                                                                                                                                                            'vakhlakov' =>
                                                                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                                        [1],
                                                                                                                                                                        [
                                                                                                                                                                            '/' =>
                                                                                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                                                        [8],
                                                                                                                                                                                        [
                                                                                                                                                                                            'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                                                        ]
                                                                                                                                                                                    ),
                                                                                                                                                                                    new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                                                ),
                                                                                                                                                                        ]
                                                                                                                                                                    ),
                                                                                                                                                                    new DefaultLeafNode(3)
                                                                                                                                                                ),
                                                                                                                                                        ]
                                                                                                                                                    ),
                                                                                                                                                    new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                ),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new DefaultLeafNode(3)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                                        new DefaultInternalNode(
                                                                                                                            [1],
                                                                                                                            [
                                                                                                                                '/' =>
                                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                                        new DefaultInternalNode(
                                                                                                                                            [9],
                                                                                                                                            [
                                                                                                                                                'vakhlakov' =>
                                                                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                                                                        new DefaultInternalNode(
                                                                                                                                                            [1],
                                                                                                                                                            [
                                                                                                                                                                '/' =>
                                                                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                                                                        new DefaultInternalNode(
                                                                                                                                                                            [8],
                                                                                                                                                                            [
                                                                                                                                                                                'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                                            ]
                                                                                                                                                                        ),
                                                                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                                    ),
                                                                                                                                                            ]
                                                                                                                                                        ),
                                                                                                                                                        new DefaultLeafNode(3)
                                                                                                                                                    ),
                                                                                                                                            ]
                                                                                                                                        ),
                                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                    ),
                                                                                                                            ]
                                                                                                                        ),
                                                                                                                        new DefaultLeafNode(3)
                                                                                                                    )
                                                                                                                ),
                                                                                                            'sily/' =>
                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                    new DefaultInternalNode(
                                                                                                                        [5],
                                                                                                                        [
                                                                                                                            'belov' =>
                                                                                                                                new DefaultInternalNodeWithLeafNode(
                                                                                                                                    new DefaultInternalNode(
                                                                                                                                        [1],
                                                                                                                                        [
                                                                                                                                            '/' =>
                                                                                                                                                new DefaultConflictResolvingNode(
                                                                                                                                                    new DefaultInternalNode(
                                                                                                                                                        [5],
                                                                                                                                                        [
                                                                                                                                                            'htonc' => new DefaultLeafNode(1),
                                                                                                                                                        ]
                                                                                                                                                    ),
                                                                                                                                                    new LeafNodeWithTerminatingPlaceholder(1)
                                                                                                                                                ),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new DefaultLeafNode(1)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                                        new DefaultInternalNode(
                                                                                                                            [6],
                                                                                                                            [
                                                                                                                                '/htonc' => new DefaultLeafNode(1),
                                                                                                                            ]
                                                                                                                        ),
                                                                                                                        new DefaultLeafNode(1)
                                                                                                                    )
                                                                                                                ),
                                                                                                        ]
                                                                                                    ),
                                                                                            ]
                                                                                        ),
                                                                                        new InternalNodeWithPlaceholderPrefix(
                                                                                            [1],
                                                                                            [
                                                                                                '/' =>
                                                                                                    new DefaultConflictResolvingNode(
                                                                                                        new DefaultInternalNode(
                                                                                                            [9],
                                                                                                            [
                                                                                                                'vakhlakov' =>
                                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                                        new DefaultInternalNode(
                                                                                                                            [1],
                                                                                                                            [
                                                                                                                                '/' =>
                                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                                        new DefaultInternalNode(
                                                                                                                                            [9],
                                                                                                                                            [
                                                                                                                                                'vakhlakov' =>
                                                                                                                                                    new DefaultInternalNodeWithLeafNode(
                                                                                                                                                        new DefaultInternalNode(
                                                                                                                                                            [1],
                                                                                                                                                            [
                                                                                                                                                                '/' =>
                                                                                                                                                                    new DefaultConflictResolvingNode(
                                                                                                                                                                        new DefaultInternalNode(
                                                                                                                                                                            [8],
                                                                                                                                                                            [
                                                                                                                                                                                'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                                            ]
                                                                                                                                                                        ),
                                                                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                                    ),
                                                                                                                                                            ]
                                                                                                                                                        ),
                                                                                                                                                        new DefaultLeafNode(3)
                                                                                                                                                    ),
                                                                                                                                            ]
                                                                                                                                        ),
                                                                                                                                        new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                    ),
                                                                                                                            ]
                                                                                                                        ),
                                                                                                                        new DefaultLeafNode(3)
                                                                                                                    ),
                                                                                                            ]
                                                                                                        ),
                                                                                                        new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                                                                                            new DefaultInternalNode(
                                                                                                                [1],
                                                                                                                [
                                                                                                                    '/' =>
                                                                                                                        new DefaultConflictResolvingNode(
                                                                                                                            new DefaultInternalNode(
                                                                                                                                [9],
                                                                                                                                [
                                                                                                                                    'vakhlakov' =>
                                                                                                                                        new DefaultInternalNodeWithLeafNode(
                                                                                                                                            new DefaultInternalNode(
                                                                                                                                                [1],
                                                                                                                                                [
                                                                                                                                                    '/' =>
                                                                                                                                                        new DefaultConflictResolvingNode(
                                                                                                                                                            new DefaultInternalNode(
                                                                                                                                                                [8],
                                                                                                                                                                [
                                                                                                                                                                    'buldakov' => new DefaultLeafNode(3),
                                                                                                                                                                ]
                                                                                                                                                            ),
                                                                                                                                                            new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                                                        ),
                                                                                                                                                ]
                                                                                                                                            ),
                                                                                                                                            new DefaultLeafNode(3)
                                                                                                                                        ),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new LeafNodeWithTerminatingPlaceholder(3)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                            new DefaultLeafNode(3)
                                                                                                        )
                                                                                                    ),
                                                                                            ]
                                                                                        )
                                                                                    ),
                                                                            ]
                                                                        ),
                                                                        new DefaultLeafNode(8)
                                                                    )
                                                                ),
                                                            's/' =>
                                                                new InternalNodeWithPlaceholderPrefix(
                                                                    [10],
                                                                    [
                                                                        '/tabakovs/' => new LeafNodeWithTerminatingPlaceholder(4),
                                                                    ]
                                                                ),
                                                            'ich/' =>
                                                                new InternalNodeWithPlaceholderPrefix(
                                                                    [1],
                                                                    [
                                                                        '/' =>
                                                                            new InternalNodeWithPlaceholderPrefix(
                                                                                [6],
                                                                                [
                                                                                    '/belov' => new DefaultLeafNode(2),
                                                                                ]
                                                                            ),
                                                                    ]
                                                                ),
                                                        ]
                                                    ),
                                                    new DefaultLeafNode(5)
                                                ),
                                        ]
                                    ),
                                'y/' =>
                                    new DefaultConflictResolvingNode(
                                        new DefaultInternalNode(
                                            [5],
                                            [
                                                'belov' => new DefaultLeafNode(6),
                                            ]
                                        ),
                                        new LeafNodeWithTerminatingPlaceholder(0)
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
