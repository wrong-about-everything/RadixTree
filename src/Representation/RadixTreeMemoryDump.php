<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Representation;

use Closure;
use Exception;
use WrongAboutEverything\RadixTree\ReadModel\Node\ConflictResolvingNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultConflictResolvingNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\RadixTreeNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\ConflictResolving;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\InternalNode as InternalNodeType;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\LeafNode as LeafNodeType;

/**
 * This is effectively a dump of radix tree in-memory representation:
 * it's represented as php code almost exactly the way it's represented in php process memory.
 */
class RadixTreeMemoryDump
{
    private $internalNode;

    public function __construct(InternalNode $internalNode)
    {
        $this->internalNode = $internalNode;
    }

    public function value(): string
    {
        return $this->nodeString($this->internalNode, 1);
    }

    private function nodeString(RadixTreeNode $node, int $tabsCount): string
    {
        switch ($node->type()->value()) {
            case (new InternalNodeType())->value():
                return $this->internalNodeString($node, $tabsCount);

            case (new LeafNodeType())->value():
                return $this->leafNodeString($node);

            case (new ConflictResolving())->value():
                return $this->compositeNodeString($node, $tabsCount);

            default:
                throw new Exception(sprintf('You have forgotten to write a case clause for type %d', $node->type()->value()));
        }
    }

    private function leafNodeString(LeafNode $node): string
    {
        return
            sprintf(
                'new %s(%d)',
                get_class($node),
                $node->id()
            );
    }

    private function compositeNodeString(ConflictResolvingNode $node, int $tabsCount): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    <<<q

new %s(%s,
    %s
)
q
                    ,
                    DefaultConflictResolvingNode::class,
                    $this->nodeString($node->nodeWithFixedKeys(), 1),
                    $this->nodeString($node->nodeWithPlaceholders(), 1)
                ),
                $tabsCount
            );
    }

    private function internalNodeString(InternalNode $node, int $tabsCount): string
    {
        switch (get_class($node)) {
            case DefaultInternalNode::class:
            case InternalNodeWithPlaceholderPrefix::class:
                return $this->defaultInternalNodeString($node, $tabsCount);

            case DefaultInternalNodeWithLeafNode::class:
            case InternalNodeWithPlaceholderPrefixAndLeafNode::class:
                return $this->internalNodeWithLeafNodeString($node, $tabsCount);

            default:
                throw new Exception(sprintf('There is no implementation for class %s', get_class($node)));
        }
    }

    private function defaultInternalNodeString(InternalNode $node, int $tabsCount): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    <<<q

new %s(
    [%s],
    [
%s
    ]
)
q
                    ,
                    get_class($node),
                    implode(', ', $node->keyLengths()),
                    rtrim(
                        implode(
                            '',
                            $this->assocArrayReduce(
                                array_combine(
                                    array_keys($node->keyToNodeArray()),
                                    array_map(
                                        function (RadixTreeNode $node) {
                                            /**
                                             * I pass 1 in the second argument so that the second line in the following expression
                                             *  <key> =>
                                             *  <internal node>
                                             * is one tab right. Keep in mind that a pattern for internal node start with a new line followed by the very beginning of the next line.
                                             */
                                            return $this->nodeString($node, 1);
                                        },
                                        $node->keyToNodeArray()
                                    )
                                ),
                                function (array $acc, string $nodeString, string $key) use ($tabsCount) {
                                    /*
                                     I pass 2 tabs so that the first node key in out pattern
                                        new %s(
                                            [%s],
                                            [
                                        %s
                                            ]
                                        )
                                    started with 2 tabs right relative to 'new'.
                                    Keep in mind that tabs count are relative to current expression, because the whole string is already wrapped with tabbedLines().
                                     */
                                    $acc[] = $this->tabbedLines(sprintf('\'%s\' => %s,', $key, $nodeString), 2) . "\n";
                                    return $acc;
                                }
                            )
                        )
                    )
                ),
                $tabsCount
            );
    }

    private function internalNodeWithLeafNodeString(InternalNodeWithLeafNode $node, int $tabsCount): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    <<<q

new %s(%s,
    %s
)
q
                    ,
                    get_class($node),
                    $this->internalNodeString($node->internalNode(), 1),
                    $this->leafNodeString($node->leafNode())
                ),
                $tabsCount
            );
    }

    private function assocArrayReduce(array $array, Closure $closure): array
    {
        $acc = [];
        foreach ($array as $key => $nodeString) {
            $acc = $closure($acc, $nodeString, (string) $key);
        }

        return $acc;
    }

    private function tabbedLines(string $nodeString, int $tabs)
    {
        $spaceIndentation = str_repeat(' ', $tabs * 4); // 1 tab = 4 spaces
        return $spaceIndentation . str_replace("\n", "\n$spaceIndentation", $nodeString);
    }
}
