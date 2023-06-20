<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Tests\Representation;

use PHPUnit\Framework\TestCase;
use WrongAboutEverything\RadixTree\Generation\DataItem;
use WrongAboutEverything\RadixTree\Generation\RadixTreeGenerator;
use WrongAboutEverything\RadixTree\Representation\RadixTreeMemoryDump;

class RadixTreeMemoryDumpTest extends TestCase
{
    public function testKeysMatchRegEx()
    {
        $radixTreeMemoryDump = (new RadixTreeMemoryDump((new RadixTreeGenerator($this->dataItems()))->value()))->value();

        $this->assertEquals($this->radixTreeDump(), $radixTreeMemoryDump);
        $this->lookUpExistingKeysSuccessfully($this->dataItems(), $radixTreeMemoryDump);
    }

    public function testKeysDontMatchRegEx()
    {
        $radixTreeMemoryDump = (new RadixTreeMemoryDump((new RadixTreeGenerator($this->dataItems()))->value()))->value();

        $this->assertEquals($this->radixTreeDump(), $radixTreeMemoryDump);
        $this->lookUpNonExistingKeysAndFindNothing($this->dataItems(), $radixTreeMemoryDump);
    }

    private function dataItems(): array
    {
        return [
            $this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov', 0),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/:vakhlakov', 2),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov', 1),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov', 3),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov/buldakov', 4),
            $this->dataItem('/vasilev/baton/:vakhlakov/:vakhlakov/vakhlakov/:buldakov', 5),
            $this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov/:vakhlakov', 6),
            $this->dataItem('/vasilev/baton/:vakhlakov/vakhlakov/vakhlakov', 7),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov', 8),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/:vakhlakov', 9),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov', 10),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov/buldakov', 11),
            $this->dataItem('/vasilev/baton/vakhlakov/:vakhlakov/vakhlakov/:buldakov', 12),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov', 13),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/:vakhlakov', 14),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov', 15),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov/buldakov', 16),
            $this->dataItem('/vasilev/baton/vakhlakov/vakhlakov/vakhlakov/:buldakov', 17),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/:vakhlakov', 18),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov', 19),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov/buldakov', 20),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/vakhlakov/:buldakov', 21),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov', 22),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov', 23),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov', 24),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov/buldakov', 25),
            $this->dataItem('/vasilev/:baton/vakhlakov/:vakhlakov/vakhlakov/:buldakov', 26),
            $this->dataItem('/vasilev/:baton/vakhlakov/vakhlakov/:vakhlakov', 27),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov', 28),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/:vakhlakov', 29),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov', 30),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov/buldakov', 31),
            $this->dataItem('/vasilev/:baton/:vakhlakov/:vakhlakov/vakhlakov/:buldakov', 32),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov', 33),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/:vakhlakov', 34),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov', 35),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov/buldakov', 36),
            $this->dataItem('/vasilev/:baton/:vakhlakov/vakhlakov/vakhlakov/:buldakov', 37),
            $this->dataItem('/vasilev/makarevi/:vasily/:belov', 38),
            $this->dataItem('/vasile/:anton/vakhlakov', 39),
            $this->dataItem('/vasily/:lebov', 40),
            $this->dataItem('/vasilev/makarev', 41),
            $this->dataItem('/vasilev/makarev/:vasily/:belov', 42),
            $this->dataItem('/vasilev/:anton/vasily/:belov', 43),
            $this->dataItem('/vasilev/:anton/vasily/belov', 44),
            $this->dataItem('/vasilev/:anton/vasily/belov/:htonc', 45),
            $this->dataItem('/vasilev/:anton/vasily/:belov/htonc', 46),
            $this->dataItem('/vasilev/:anton/vasily/belov/htonc', 47),
            $this->dataItem('/vasilev/makarevi', 48),
            $this->dataItem('/vasilevs/:antons/tabakovs/:matskyavichus', 49),
            $this->dataItem('/vasily/belov', 50),
            $this->dataItem('/vasilev/:anton', 51),
            $this->dataItem('/vasilev/:anton/anton', 52),
            $this->dataItem('/vasilev/:anton/anton/:petrovich', 53),
            $this->dataItem('/vasilev/:anton/anton/:petrovich/anton', 54),
            $this->dataItem('/vasilev/:anton/anton/:petrovich/anton/:vasilyev', 55),
            $this->dataItem('/vasilev/anton/:anton/petrovich/:anton/vasilyev', 56),
            $this->dataItem('/vasilev/anton/:anton/petrovich/:anton', 57),
            $this->dataItem('/vasilev/anton/:anton/petrovich', 58),
            $this->dataItem('/vasilev/anton/:anton', 59),
            $this->dataItem('/vasilev/anton', 60),
            $this->dataItem('/vasilevich/:makarevich/:vasily/belov', 61),
            $this->dataItem('/vasile/antoine/vakhlakov', 62),
            $this->dataItem('/vasilev', 63),
            $this->dataItem('/vasilev/makarevich', 64),
            $this->dataItem('/vasilev/makarevich/vasilev', 65),
            $this->dataItem('/vasilev/makarevich/vasilev/makarevich', 66),
            $this->dataItem('/vasilev/makarevich/vasilev/makarevich/vasilev', 67),
            $this->dataItem('/vasile/:anton', 68),
        ];
    }

    private function radixTreeDump(): string
    {
        return
            <<<d
    
    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
        [6],
        [
            '/vasil' =>     
                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                    [1, 2],
                    [
                        'e' =>     
                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                [1],
                                [
                                    '/' =>     
                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                [17],
                                                [
                                                    'antoine/vakhlakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(62),
                                                ]
                                            ),
                                                
                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                    [10],
                                                    [
                                                        '/vakhlakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(39),
                                                    ]
                                                ),
                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(68)
                                            )
                                        ),
                                    'v' =>     
                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                [1, 2, 4],
                                                [
                                                    '/' =>     
                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                [5, 6, 7],
                                                                [
                                                                    'baton/' =>     
                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                [10],
                                                                                [
                                                                                    'vakhlakov/' =>     
                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                [9],
                                                                                                [
                                                                                                    'vakhlakov' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                [1],
                                                                                                                [
                                                                                                                    '/' =>     
                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                [9],
                                                                                                                                [
                                                                                                                                    'vakhlakov' =>     
                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                [1],
                                                                                                                                                [
                                                                                                                                                    '/' =>     
                                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                                [8],
                                                                                                                                                                [
                                                                                                                                                                    'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(16),
                                                                                                                                                                ]
                                                                                                                                                            ),
                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(17)
                                                                                                                                                        ),
                                                                                                                                                ]
                                                                                                                                            ),
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(15)
                                                                                                                                        ),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(14)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(13)
                                                                                                        ),
                                                                                                ]
                                                                                            ),
                                                                                                
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                    [1],
                                                                                                    [
                                                                                                        '/' =>     
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                    [9],
                                                                                                                    [
                                                                                                                        'vakhlakov' =>     
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                    [1],
                                                                                                                                    [
                                                                                                                                        '/' =>     
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                    [8],
                                                                                                                                                    [
                                                                                                                                                        'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(11),
                                                                                                                                                    ]
                                                                                                                                                ),
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(12)
                                                                                                                                            ),
                                                                                                                                    ]
                                                                                                                                ),
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(10)
                                                                                                                            ),
                                                                                                                    ]
                                                                                                                ),
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(9)
                                                                                                            ),
                                                                                                    ]
                                                                                                ),
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(8)
                                                                                            )
                                                                                        ),
                                                                                ]
                                                                            ),
                                                                                
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                                                [1],
                                                                                [
                                                                                    '/' =>     
                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                [9],
                                                                                                [
                                                                                                    'vakhlakov' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                [1],
                                                                                                                [
                                                                                                                    '/' =>     
                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                [9],
                                                                                                                                [
                                                                                                                                    'vakhlakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(7),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(6)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(0)
                                                                                                        ),
                                                                                                ]
                                                                                            ),
                                                                                                
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                    [1],
                                                                                                    [
                                                                                                        '/' =>     
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                    [9],
                                                                                                                    [
                                                                                                                        'vakhlakov' =>     
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                    [1],
                                                                                                                                    [
                                                                                                                                        '/' =>     
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                    [8],
                                                                                                                                                    [
                                                                                                                                                        'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(4),
                                                                                                                                                    ]
                                                                                                                                                ),
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(5)
                                                                                                                                            ),
                                                                                                                                    ]
                                                                                                                                ),
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(3)
                                                                                                                            ),
                                                                                                                    ]
                                                                                                                ),
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(2)
                                                                                                            ),
                                                                                                    ]
                                                                                                ),
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(1)
                                                                                            )
                                                                                        ),
                                                                                ]
                                                                            )
                                                                        ),
                                                                    'makarev' =>     
                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                [1],
                                                                                [
                                                                                    '/' =>     
                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                                                            [1],
                                                                                            [
                                                                                                '/' => new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(42),
                                                                                            ]
                                                                                        ),
                                                                                    'i' =>     
                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                [1, 2],
                                                                                                [
                                                                                                    '/' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                                                                            [1],
                                                                                                            [
                                                                                                                '/' => new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(38),
                                                                                                            ]
                                                                                                        ),
                                                                                                    'ch' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                [8],
                                                                                                                [
                                                                                                                    '/vasilev' =>     
                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                [11],
                                                                                                                                [
                                                                                                                                    '/makarevich' =>     
                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                [8],
                                                                                                                                                [
                                                                                                                                                    '/vasilev' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(67),
                                                                                                                                                ]
                                                                                                                                            ),
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(66)
                                                                                                                                        ),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(65)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(64)
                                                                                                        ),
                                                                                                ]
                                                                                            ),
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(48)
                                                                                        ),
                                                                                ]
                                                                            ),
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(41)
                                                                        ),
                                                                    'anton' =>     
                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                [1],
                                                                                [
                                                                                    '/' =>     
                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                [10],
                                                                                                [
                                                                                                    '/petrovich' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                [1],
                                                                                                                [
                                                                                                                    '/' =>     
                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                [9],
                                                                                                                                [
                                                                                                                                    '/vasilyev' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(56),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(57)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(58)
                                                                                                        ),
                                                                                                ]
                                                                                            ),
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(59)
                                                                                        ),
                                                                                ]
                                                                            ),
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(60)
                                                                        ),
                                                                ]
                                                            ),
                                                                
                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                    [1],
                                                                    [
                                                                        '/' =>     
                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                    [2, 5],
                                                                                    [
                                                                                        'va' =>     
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                [5, 8],
                                                                                                [
                                                                                                    'khlakov/' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                [9],
                                                                                                                [
                                                                                                                    'vakhlakov' =>     
                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                [1],
                                                                                                                                [
                                                                                                                                    '/' =>     
                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                [9],
                                                                                                                                                [
                                                                                                                                                    'vakhlakov' =>     
                                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                                [1],
                                                                                                                                                                [
                                                                                                                                                                    '/' =>     
                                                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                                                [8],
                                                                                                                                                                                [
                                                                                                                                                                                    'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(20),
                                                                                                                                                                                ]
                                                                                                                                                                            ),
                                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(21)
                                                                                                                                                                        ),
                                                                                                                                                                ]
                                                                                                                                                            ),
                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(19)
                                                                                                                                                        ),
                                                                                                                                                ]
                                                                                                                                            ),
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(27)
                                                                                                                                        ),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(22)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                                
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                    [1],
                                                                                                                    [
                                                                                                                        '/' =>     
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                    [9],
                                                                                                                                    [
                                                                                                                                        'vakhlakov' =>     
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                    [1],
                                                                                                                                                    [
                                                                                                                                                        '/' =>     
                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                                    [8],
                                                                                                                                                                    [
                                                                                                                                                                        'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(25),
                                                                                                                                                                    ]
                                                                                                                                                                ),
                                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(26)
                                                                                                                                                            ),
                                                                                                                                                    ]
                                                                                                                                                ),
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(24)
                                                                                                                                            ),
                                                                                                                                    ]
                                                                                                                                ),
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(18)
                                                                                                                            ),
                                                                                                                    ]
                                                                                                                ),
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(23)
                                                                                                            )
                                                                                                        ),
                                                                                                    'sily/' =>     
                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                [5],
                                                                                                                [
                                                                                                                    'belov' =>     
                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                [1],
                                                                                                                                [
                                                                                                                                    '/' =>     
                                                                                                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                [5],
                                                                                                                                                [
                                                                                                                                                    'htonc' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(47),
                                                                                                                                                ]
                                                                                                                                            ),
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(45)
                                                                                                                                        ),
                                                                                                                                ]
                                                                                                                            ),
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(44)
                                                                                                                        ),
                                                                                                                ]
                                                                                                            ),
                                                                                                                
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                    [6],
                                                                                                                    [
                                                                                                                        '/htonc' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(46),
                                                                                                                    ]
                                                                                                                ),
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(43)
                                                                                                            )
                                                                                                        ),
                                                                                                ]
                                                                                            ),
                                                                                        'anton' =>     
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                    [1],
                                                                                                    [
                                                                                                        '/' =>     
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                    [6],
                                                                                                                    [
                                                                                                                        '/anton' =>     
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                    [1],
                                                                                                                                    [
                                                                                                                                        '/' => new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(55),
                                                                                                                                    ]
                                                                                                                                ),
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(54)
                                                                                                                            ),
                                                                                                                    ]
                                                                                                                ),
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(53)
                                                                                                            ),
                                                                                                    ]
                                                                                                ),
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(52)
                                                                                            ),
                                                                                    ]
                                                                                ),
                                                                                    
                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                                                    [1],
                                                                                    [
                                                                                        '/' =>     
                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                    [9],
                                                                                                    [
                                                                                                        'vakhlakov' =>     
                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                    [1],
                                                                                                                    [
                                                                                                                        '/' =>     
                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                    [9],
                                                                                                                                    [
                                                                                                                                        'vakhlakov' =>     
                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                    [1],
                                                                                                                                                    [
                                                                                                                                                        '/' =>     
                                                                                                                                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                                    [8],
                                                                                                                                                                    [
                                                                                                                                                                        'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(36),
                                                                                                                                                                    ]
                                                                                                                                                                ),
                                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(37)
                                                                                                                                                            ),
                                                                                                                                                    ]
                                                                                                                                                ),
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(35)
                                                                                                                                            ),
                                                                                                                                    ]
                                                                                                                                ),
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(34)
                                                                                                                            ),
                                                                                                                    ]
                                                                                                                ),
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(33)
                                                                                                            ),
                                                                                                    ]
                                                                                                ),
                                                                                                    
                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode(    
                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                        [1],
                                                                                                        [
                                                                                                            '/' =>     
                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                        [9],
                                                                                                                        [
                                                                                                                            'vakhlakov' =>     
                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode(    
                                                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                        [1],
                                                                                                                                        [
                                                                                                                                            '/' =>     
                                                                                                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                                                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                                                                                                                                        [8],
                                                                                                                                                        [
                                                                                                                                                            'buldakov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(31),
                                                                                                                                                        ]
                                                                                                                                                    ),
                                                                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(32)
                                                                                                                                                ),
                                                                                                                                        ]
                                                                                                                                    ),
                                                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(30)
                                                                                                                                ),
                                                                                                                        ]
                                                                                                                    ),
                                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(29)
                                                                                                                ),
                                                                                                        ]
                                                                                                    ),
                                                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(28)
                                                                                                )
                                                                                            ),
                                                                                    ]
                                                                                )
                                                                            ),
                                                                    ]
                                                                ),
                                                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(51)
                                                            )
                                                        ),
                                                    's/' =>     
                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                            [10],
                                                            [
                                                                '/tabakovs/' => new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(49),
                                                            ]
                                                        ),
                                                    'ich/' =>     
                                                        new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                            [1],
                                                            [
                                                                '/' =>     
                                                                    new WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix(
                                                                        [6],
                                                                        [
                                                                            '/belov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(61),
                                                                        ]
                                                                    ),
                                                            ]
                                                        ),
                                                ]
                                            ),
                                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(63)
                                        ),
                                ]
                            ),
                        'y/' =>     
                            new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode(    
                                new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode(
                                    [5],
                                    [
                                        'belov' => new WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode(50),
                                    ]
                                ),
                                new WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder(40)
                            ),
                    ]
                ),
        ]
    )
d;
    }

    /**
     * @param DataItem[] $dataItems
     * @param string $radixTreeDump
     */
    private function lookUpExistingKeysSuccessfully(array $dataItems, string $radixTreeDump)
    {
        $radixTree = eval(sprintf('return %s;', $radixTreeDump));
        foreach ($dataItems as $dataItem) {
            $queryStringAndGeneratedPlaceholders = $this->queryStringAndGeneratedPlaceholders($dataItem->key());
            $result = $radixTree->result($queryStringAndGeneratedPlaceholders[0]);
            $this->assertTrue($result->isFound());
            $this->assertEquals($queryStringAndGeneratedPlaceholders[1], $result->values());
            $this->assertEquals($dataItem->id(), $result->nodeId());
        }
    }

    private function lookUpNonExistingKeysAndFindNothing(array $dataItems, string $radixTreeDump)
    {
        $radixTree = eval(sprintf('return %s;', $radixTreeDump));

        $characters = 'abcdef/ghijklm/nopqrstu/vwxyzAB/CDEFGHI/JKLMNOPQ/RSTUVW/XYZ0123/456789';
        for ($i = 0; $i < count($dataItems); $i++) {
            $randomString = substr(str_shuffle($characters), 0, mt_rand(1, 60));
            $result = $radixTree->result($randomString);
            $this->assertFalse($result->isFound());
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

    private function dataItem(string $path, int $index): DataItem
    {
        return new DataItem($path, $index);
    }
}