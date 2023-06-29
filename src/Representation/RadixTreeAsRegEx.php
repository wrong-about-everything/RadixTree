<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Representation;

use Closure;
use Exception;
use WrongAboutEverything\RadixTree\ReadModel\Node\ConflictResolvingNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder;
use WrongAboutEverything\RadixTree\ReadModel\Node\RadixTreeNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\ConflictResolving;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\InternalNode as InternalNodeType;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\LeafNode as LeafNodeType;

class RadixTreeAsRegEx
{
    private $internalNode;
    private $debug;

    public function __construct(InternalNode $internalNode, bool $debug)
    {
        $this->internalNode = $internalNode;
        $this->debug = $debug;
    }

    public function value(): string
    {
        return sprintf('#%s#', $this->regEx($this->internalNode, 0, false));
    }

    private function regEx(RadixTreeNode $node, int $tabsCount, bool $newLine): string
    {
        switch ($node->type()->value()) {
            case (new InternalNodeType())->value():
                return $this->internalNodeString($node, $tabsCount, $newLine);

            case (new LeafNodeType())->value():
                return $this->leafNodeString($node);

            case (new ConflictResolving())->value():
                return $this->compositeNodeString($node, $tabsCount, true);

            default:
                throw new Exception(sprintf('You have forgotten to write a case clause for type %d', $node->type()->value()));
        }
    }

    private function leafNodeString(LeafNode $node): string
    {
        switch (get_class($node)) {
            case DefaultLeafNode::class:
                return $this->defaultLeafNodeString($node);
            case LeafNodeWithTerminatingPlaceholder::class:
                return $this->leafNodeWithTerminatingPlaceholderString($node);

            default:
                throw new Exception();
        }
    }

    private function defaultLeafNodeString(DefaultLeafNode $node): string
    {
        return
            sprintf(
                '(*:%d)',
                $node->id()
            );
    }

    // This function can be called with DefaultLeafNode. See self::internalNodeWithPlaceholderPrefixAndLeafNodeString() for instance.
    private function leafNodeWithTerminatingPlaceholderString(LeafNode $node): string
    {
        return
            sprintf(
                '([^/]+)(*:%d)',
                $node->id()
            );
    }

    private function compositeNodeString(ConflictResolvingNode $node, int $tabsCount, bool $newLine): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    '(?|%s|%s)',
                    $this->regEx($node->nodeWithFixedKeys(), 1, true),
                    $this->regEx($node->nodeWithPlaceholders(), 1, true)
                ),
                $tabsCount,
                $newLine
            );
    }

    private function internalNodeString(InternalNode $node, int $tabsCount, bool $newLine): string
    {
        switch (get_class($node)) {
            case DefaultInternalNode::class:
                return $this->defaultInternalNodeString($node, $tabsCount, $newLine);
            case InternalNodeWithPlaceholderPrefix::class:
                return $this->internalNodeWithPlaceholderPrefixString($node, $tabsCount, $newLine);

            case DefaultInternalNodeWithLeafNode::class:
                return $this->internalNodeWithLeafNodeString($node, $tabsCount, $newLine);
            case InternalNodeWithPlaceholderPrefixAndLeafNode::class:
                return $this->internalNodeWithPlaceholderPrefixAndLeafNodeString($node, $tabsCount, $newLine);

            default:
                throw new Exception(sprintf('There is no implementation for class %s', get_class($node)));
        }
    }

    private function defaultInternalNodeString(InternalNode $node, int $tabsCount, bool $newLine): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    '(?|%s)',
                    implode(
                        '|',
                        $this->assocArrayReduce(
                            array_combine(
                                array_keys($node->keyToNodeArray()),
                                array_map(
                                    function (RadixTreeNode $node) {
                                        return $this->regEx($node, 1, true);
                                    },
                                    $node->keyToNodeArray()
                                )
                            ),
                            function (array $acc, string $nodeString, string $key) use ($tabsCount, $newLine) {
                                $acc[] = $this->tabbedLines(sprintf('%s%s', $key, $nodeString), 0, $newLine);
                                return $acc;
                            }
                        )
                    )
                ),
                $tabsCount,
                $newLine
            );
    }

    private function internalNodeWithPlaceholderPrefixString(InternalNode $node, int $tabsCount, bool $newLine): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    '([^/]+)(?|%s)',
                    implode(
                        '|',
                        $this->assocArrayReduce(
                            array_combine(
                                array_keys($node->keyToNodeArray()),
                                array_map(
                                    function (RadixTreeNode $node) {
                                        return $this->regEx($node, 1, true);
                                    },
                                    $node->keyToNodeArray()
                                )
                            ),
                            function (array $acc, string $nodeString, string $key) use ($tabsCount) {
                                $acc[] = sprintf('%s%s', $key, $nodeString);
                                return $acc;
                            }
                        )
                    )
                ),
                $tabsCount,
                $newLine
            );
    }

    private function internalNodeWithLeafNodeString(InternalNodeWithLeafNode $node, int $tabsCount, bool $newLine): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    '%s(?|%s)?',
                    $this->leafNodeString($node->leafNode()),
                    $this->internalNodeString($node->internalNode(), 0, true)
                ),
                $tabsCount,
                $newLine
            );
    }

    private function internalNodeWithPlaceholderPrefixAndLeafNodeString(InternalNodeWithPlaceholderPrefixAndLeafNode $node, int $tabsCount, bool $newLine): string
    {
        return
            $this->tabbedLines(
                sprintf(
                    '%s(?|%s)?',
                    $this->leafNodeWithTerminatingPlaceholderString($node->leafNode()),
                    $this->internalNodeString($node->internalNode(), 0, true)
                ),
                $tabsCount,
                $newLine
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

    private function tabbedLines(string $nodeString, int $tabs, bool $newLine)
    {
        if (!$this->debug) {
            return $nodeString;
        }

        $spaceIndentation = str_repeat(' ', $tabs * 4); // 1 tab = 4 spaces
        return ($newLine ? "\n" : '') . $spaceIndentation . str_replace("\n", "\n$spaceIndentation", $nodeString);
    }
}
