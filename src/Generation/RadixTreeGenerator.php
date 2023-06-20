<?php

declare(strict_types=1);

namespace WrongAboutEverything\RadixTree\Generation;

use Exception;
use WrongAboutEverything\RadixTree\ReadModel\Node\ConflictResolvingNode_Internal_SecondParamPassedByRef;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultInternalNodeWithLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\DefaultLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefix;
use WrongAboutEverything\RadixTree\ReadModel\Node\InternalNodeWithPlaceholderPrefixAndLeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNode;
use WrongAboutEverything\RadixTree\ReadModel\Node\LeafNodeWithTerminatingPlaceholder;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\ConflictResolving;
use WrongAboutEverything\RadixTree\ReadModel\Node\Type\LeafNode as LeafNodeType;

class RadixTreeGenerator
{
    /** @var DataItem[] */
    private $dataItems;
    private $allKeys;

    public function __construct(array $dataItems)
    {
        $this->dataItems = $dataItems;
        $this->allKeys = [];
    }

    public function value(): InternalNode
    {
        $root = new DefaultInternalNode([], []);

        for ($i = 0; $i <= count($this->dataItems) - 1; $i++) {
            $key = $this->dataItems[$i]->key();
            if ($this->keyAlreadyExists($key)) {
                throw new Exception(sprintf('You want to add a key %s, but you\'ve already added it', $this->dataItems[$i]->key()));
            }
            $this->addNewNode($root, $key, $this->dataItems[$i]->id(), false);
            $this->rememberKey($key);
        }

        return $root;
    }

    private function addNewNode(InternalNode &$original, string $newKey, int $index, bool $placeholderWasEaten): void
    {
        $deepestMatchingKey = '';
        $commonPart = '';
        $commonPartLength = 0;
        $deepestInternalNodeWhereToAddANode = &$this->deepestInternalNodeWhereToAddAKey($original, $newKey, $commonPart, $commonPartLength, $deepestMatchingKey, $placeholderWasEaten);

        // if a new key has nothing in common with none of keyToNodeArray() keys, then add this key there -- recursively, in case it contains a placeholder
        if (strlen($commonPart) === 0) {
            $this->addKeyToNode($deepestInternalNodeWhereToAddANode, $newKey, $index, $placeholderWasEaten);
        }
        // otherwise, split a key which has some substring common with a new key.
        // eg, existing key: 'vasily' => ... , added key = vasilevsky, hence $newKey = evsky.
        else {
            $newInternalNode = new DefaultInternalNode([], []); // dummy
            /**
             * Another example: existing key = vasile/:belov, that is, internal_node('vasile/' => leaf_node_with_placeholder).
             * Added key = vasile/:belov/vakhlakov.
             * Move leaf node into new internal one, take its reference. Eat next placeholder and add /vakhlakov.
             *
             * The result is
             * internal_node(
             *  'vasile/' =>
             *      internal_node_with_placeholder_prefix_and_leaf_node(
             *          internal_node(/vakhlakov => leaf_node),
             *          leaf_node_with_placeholder
             *      )
             * )
             */
            $this->moveExistingNodeIntoNewInternalNode($deepestInternalNodeWhereToAddANode, $newInternalNode, $deepestMatchingKey, $commonPart, $commonPartLength, $newKey, $placeholderWasEaten);
            $this->addNewNode($newInternalNode, $newKey, $index, $placeholderWasEaten);
        }
    }

    private function addKeyToNode(InternalNode &$deepestInternalNodeWhereToAddANode, string $newKey, int $index, bool $placeholderWasEaten)
    {
        if (strlen($newKey) === 0) {
            $this->convertInternalNodeToInternalNodeWithLeafNode($deepestInternalNodeWhereToAddANode, $index, $placeholderWasEaten);
        } else {
            $this->addNewLeafNodeToKeyNodesArrayOfInternalNode($deepestInternalNodeWhereToAddANode, $newKey, $index, $placeholderWasEaten);
        }
    }

    private function addNewLeafNodeToKeyNodesArrayOfInternalNode(InternalNode &$deepestInternalNodeWhereToAddANode, string $newKey, int $index, bool $placeholderWasEaten): void
    {
        $nextPlaceholderPosition = strpos($newKey, ':');
        if ($nextPlaceholderPosition === false) {
            // conflict
            if ($deepestInternalNodeWhereToAddANode->hasPlaceholder() && !$placeholderWasEaten) {
                $copiedInternalNodeWhereToAddANode = $deepestInternalNodeWhereToAddANode;
                $deepestInternalNodeWhereToAddANode =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        new DefaultInternalNode([strlen($newKey)], [$newKey => new DefaultLeafNode($index)]),
                        $copiedInternalNodeWhereToAddANode
                    );
            } elseif (!$deepestInternalNodeWhereToAddANode->hasPlaceholder() && $placeholderWasEaten) {
                $copiedInternalNodeWhereToAddANode = $deepestInternalNodeWhereToAddANode;
                $newNode = new InternalNodeWithPlaceholderPrefix([strlen($newKey)], [$newKey => new DefaultLeafNode($index)]);
                $deepestInternalNodeWhereToAddANode =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        $copiedInternalNodeWhereToAddANode,
                        $newNode
                    );
            } else {
                $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$newKey] = new DefaultLeafNode($index);
                $this->addKeyLength($deepestInternalNodeWhereToAddANode, $newKey);
            }
        } elseif (strpos($newKey, ':', $nextPlaceholderPosition + 1) === false && strpos($newKey, '/', $nextPlaceholderPosition + 1) === false) {
            // if found placeholder is the last one AND it's in a tail, then add a LeafNodeWithTerminating placeholder
            // e.g., $newKey = /vasily/:belov
            $currentKey = substr($newKey, 0, $nextPlaceholderPosition);
            if ($deepestInternalNodeWhereToAddANode->hasPlaceholder() && !$placeholderWasEaten) {
                $copiedExistingNode = $deepestInternalNodeWhereToAddANode;
                $deepestInternalNodeWhereToAddANode =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        new DefaultInternalNode([strlen($currentKey)], [$currentKey => new LeafNodeWithTerminatingPlaceholder($index)]),
                        $copiedExistingNode
                    );
            } elseif (!$deepestInternalNodeWhereToAddANode->hasPlaceholder() && $placeholderWasEaten) {
                $copiedExistingNode = $deepestInternalNodeWhereToAddANode;
                $newNode = new InternalNodeWithPlaceholderPrefix([strlen($currentKey)], [$currentKey => new LeafNodeWithTerminatingPlaceholder($index)]);
                $deepestInternalNodeWhereToAddANode =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        $copiedExistingNode,
                        $newNode
                    );
            } else {
                $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$currentKey] = new LeafNodeWithTerminatingPlaceholder($index);
                $this->addKeyLength($deepestInternalNodeWhereToAddANode, $currentKey);
            }
        } else {
            // if the next placeholder is NOT the last one OR it's not in a tail, add a InternalNodeWithPlaceholderPrefix node
            // eg, $newKey is vasilev/:anton/vasily/:belov OR $newKey is /:vasily/belov
            if ($deepestInternalNodeWhereToAddANode->hasPlaceholder() && !$placeholderWasEaten) {
                // conflict
                $newInternalNode = new DefaultInternalNode([], []);
                $this->addNewNode(
                    $newInternalNode,
                    $newKey,
                    $index,
                    $placeholderWasEaten // always false
                );
                $copiedExistingNode = $deepestInternalNodeWhereToAddANode;
                $deepestInternalNodeWhereToAddANode =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        $newInternalNode,
                        $copiedExistingNode
                    );
            } elseif (!$deepestInternalNodeWhereToAddANode->hasPlaceholder() && $placeholderWasEaten) {
                $newInternalNode = new InternalNodeWithPlaceholderPrefix([], []);
                $this->addNewNode(
                    $newInternalNode,
                    $newKey,
                    $index,
                    $placeholderWasEaten // always true
                );
                $copiedExistingNode = $deepestInternalNodeWhereToAddANode;
                $deepestInternalNodeWhereToAddANode =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        $copiedExistingNode,
                        $newInternalNode
                    );
            } else {
                $newInternalNode = new InternalNodeWithPlaceholderPrefix([], []);
                $currentKey = substr($newKey, 0, $nextPlaceholderPosition);
                $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$currentKey] =& $newInternalNode;
                $this->addKeyLength($deepestInternalNodeWhereToAddANode, $currentKey);
                // $newKey is 'vasilev/:anton/vasily/:belov', I pass '/vasily/:belov further'.
                // $newKey is '/:vasily/:belov', I pass '/:belov further'.
                $this->addNewNode(
                    $newInternalNode,
                    $this->keyWithEatenPlaceholder(substr($newKey, $nextPlaceholderPosition)),
                    $index,
                    true
                );
            }
        }
    }

    private function moveExistingNodeIntoNewInternalNode(InternalNode $deepestInternalNodeWhereToAddANode, InternalNode &$newInternalNode, string $deepestKeyOfDeepestNode, string $commonPartOfNewAndOldKeys, int $commonPartLength, string $newKey, bool $placeholderWasEaten): void
    {
        if ($deepestKeyOfDeepestNode === $commonPartOfNewAndOldKeys) {
            $this->assert($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] instanceof LeafNode);
            // existing key is vasil, new key is vasily. Then existing key goes into new internal node's leaf node.
            if (
                ($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] instanceof DefaultLeafNode && !$placeholderWasEaten)
                    ||
                ($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] instanceof LeafNodeWithTerminatingPlaceholder && $placeholderWasEaten)
            ) {
                $this->moveExistingNodeIntoNewInternalNodesLeaf($deepestInternalNodeWhereToAddANode, $newInternalNode, $deepestKeyOfDeepestNode);
            } else {
                // conflict.
                $newInternalNode = new DefaultInternalNode([], []);
                $oldNode = $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode];
                $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] =
                    new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                        $newInternalNode,
                        $oldNode
                    );
            }
        } else {
            // Existing key is vasily, new key is vasil.
            // Then existing key is split: 'vasil' => new_internal_node(['y' => existing_node])
            $this->assert($commonPartOfNewAndOldKeys < $deepestKeyOfDeepestNode);
            $this->moveExistingNodeIntoNewInternalNodesKeyToNodeArray($deepestInternalNodeWhereToAddANode, $newInternalNode, $deepestKeyOfDeepestNode, $commonPartOfNewAndOldKeys, $commonPartLength, $newKey, $placeholderWasEaten);
        }
    }

    private function moveExistingNodeIntoNewInternalNodesKeyToNodeArray(InternalNode $deepestInternalNodeWhereToAddANode, InternalNode &$newInternalNode, string $deepestKeyOfDeepestNode, string $commonPartOfNewAndOldKeys, int $commonPartLength, string $newKey, bool $placeholderWasEaten)
    {
        $nodeToMove = $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode];
        unset($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode]);
        $this->removeLengthOfCurrentKeyIfThereAreNoKeysOfTheSameLength($deepestInternalNodeWhereToAddANode, $deepestKeyOfDeepestNode);
        $remainingPartOfAnOldKey = substr($deepestKeyOfDeepestNode, $commonPartLength);

        if (strlen($newKey) !== 0 && $placeholderWasEaten) {
            $newInternalNode = new InternalNodeWithPlaceholderPrefix([], []);
            // I really need to pass the second arg as a reference, because later it can be *replaced* with InternalNodeWithPlaceholderPrefixAndLeafNode.
            $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$commonPartOfNewAndOldKeys] =
                new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                    new DefaultInternalNode(
                        [strlen($remainingPartOfAnOldKey)],
                        [$remainingPartOfAnOldKey => $nodeToMove]
                    ),
                    $newInternalNode
                );
            $newInternalNode = $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$commonPartOfNewAndOldKeys]->nodeWithPlaceholders();
        } else {
            $newInternalNode =
                new DefaultInternalNode(
                    [strlen($remainingPartOfAnOldKey)],
                    [(string) $remainingPartOfAnOldKey => $nodeToMove]
                );
            $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$commonPartOfNewAndOldKeys] = &$newInternalNode;
        }

        $this->addKeyLength($deepestInternalNodeWhereToAddANode, $commonPartOfNewAndOldKeys);
    }

    private function moveExistingNodeIntoNewInternalNodesLeaf(InternalNode $deepestInternalNodeWhereToAddANode, InternalNode &$newInternalNode, string $deepestKeyOfDeepestNode)
    {
        if ($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] instanceof LeafNodeWithTerminatingPlaceholder) {
            $newInternalNode =
                new InternalNodeWithPlaceholderPrefixAndLeafNode(
                    new DefaultInternalNode([], []),
                    new DefaultLeafNode($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode]->id())
                );
        } elseif ($deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] instanceof DefaultLeafNode) {
            $newInternalNode =
                new DefaultInternalNodeWithLeafNode(
                    new DefaultInternalNode([], []),
                    $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode]
                );
        } else {
            $this->assert(false);
        }

        $deepestInternalNodeWhereToAddANode->keyToNodeArray()[$deepestKeyOfDeepestNode] =& $newInternalNode;
    }

    private function &deepestInternalNodeWhereToAddAKey(InternalNode &$original, string &$newKey, string &$commonPart, int &$commonPartLength, string &$deepestMatchingKey, bool &$placeholderWasEaten): InternalNode
    {
        $this->assert(strlen($newKey) === 0 || $newKey[0] !== ':');
        $commonPart = '';
        $commonPartLength = 0;
        $keyToNodes = &$original->keyToNodeArray();

        foreach (array_keys($keyToNodes) as $currentKey) {
            $currentKey = (string) $currentKey;
            $commonPart = $this->commonKey($currentKey, $newKey, $commonPartLength);
            if (strlen($commonPart) === 0) {
                continue;
            }
            $deepestMatchingKey = $currentKey;
            $placeholderWasEaten = false;
            // Eat placeholder if it's there and move on.
            // If $keyToAdd after common part is :makarevich/:vasily/:belov, then I pass /:vasily/:belov
            // If $keyToAdd after common part is :vasily, then I pass empty string
            $newKey = $this->keyWithEatenPlaceholder(substr($newKey, $commonPartLength), $placeholderWasEaten);
            if ($commonPartLength < strlen($currentKey) || $keyToNodes[$currentKey] instanceof LeafNode) {
                return $original;
            }
            if ($keyToNodes[$currentKey]->type()->equals(new ConflictResolving())) {
                if ($placeholderWasEaten) {
                    $commonPart = '';
                    $commonPartLength = 0;
                    $nextNode =& $keyToNodes[$currentKey]->nodeWithPlaceholders();
                    // There are two cases when I modify tree structure in this method: it's when I turn leaf nodes to empty internal nodes where a key is supposed to be added.
                    // Ideally, this shouldn't have happened. I could avoid it by always creating empty internal nodes with a leaf node, that is, descendants of InternalNodeWithLeafNode class.
                    // But the tree would look ugly, and I should've fixed an equal() method.
                    // All in all, I think it's a justifiable trade-off.
                    if ($nextNode->type()->equals(new LeafNodeType())) {
                        $nextNode =
                            new InternalNodeWithPlaceholderPrefixAndLeafNode(
                                new DefaultInternalNode([], []),
                                new DefaultLeafNode($keyToNodes[$currentKey]->nodeWithPlaceholders()->id())
                            );
                    }
                } else {
                    $nextNode =& $keyToNodes[$currentKey]->nodeWithFixedKeys();
                    if ($nextNode->type()->equals(new LeafNodeType())) {
                        $nextNode = new DefaultInternalNodeWithLeafNode(new DefaultInternalNode([], []), $keyToNodes[$currentKey]->nodeWithFixedKeys());
                    }
                }
                return
                    $this->deepestInternalNodeWhereToAddAKey(
                        $nextNode,
                        $newKey,
                        $commonPart,
                        $commonPartLength,
                        $deepestMatchingKey,
                        $placeholderWasEaten
                    );
            }
            return $this->deepestInternalNodeWhereToAddAKey($keyToNodes[$currentKey], $newKey, $commonPart, $commonPartLength, $deepestMatchingKey, $placeholderWasEaten);
        }

        return $original;
    }

    private function convertInternalNodeToInternalNodeWithLeafNode(InternalNode &$internalNode, int $index, bool $placeholderWasEaten): void
    {
        switch (get_class($internalNode)) {
            case DefaultInternalNode::class:
                // conflict
                if ($placeholderWasEaten) {
                    $newLeafNode = new LeafNodeWithTerminatingPlaceholder($index);
                    $internalNode =
                        new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                            $internalNode,
                            $newLeafNode
                        );
                } else {
                    $internalNode = new DefaultInternalNodeWithLeafNode($internalNode, new DefaultLeafNode($index));
                }
                break;
            case InternalNodeWithPlaceholderPrefix::class:
                if (!$placeholderWasEaten) {
                    $internalNodeCopy = $internalNode;
                    $internalNode =
                        new ConflictResolvingNode_Internal_SecondParamPassedByRef(
                            new DefaultLeafNode($index),
                            $internalNodeCopy
                        );
                } else {
                    $internalNode =
                        new InternalNodeWithPlaceholderPrefixAndLeafNode(
                            new DefaultInternalNode(
                                $internalNode->keyLengths(),
                                $internalNode->keyToNodeArray()
                            ),
                            new DefaultLeafNode($index)
                        );
                }
                break;
            default:
                throw new Exception('I do not know how to handle ' . get_class($internalNode));
        }
    }

    private function removeLengthOfCurrentKeyIfThereAreNoKeysOfTheSameLength(InternalNode $original, string $currentKey): void
    {
        $keyLengths = &$original->keyLengths();
        foreach (array_keys($original->keyToNodeArray()) as $key) {
            if (strlen($key) === strlen($currentKey)) {
                return;
            }
        }
        $index = array_search(strlen($currentKey), $keyLengths);
        unset($keyLengths[$index]);
        $keyLengths = array_values($keyLengths);
    }

    private function addKeyLength(InternalNode $original, string $key): void
    {
        $keyLengths = &$original->keyLengths();
        if (!in_array(strlen($key), $keyLengths)) {
            array_push($keyLengths, strlen($key));
            sort($keyLengths);
            $keyLengths = array_values($keyLengths);
        }
    }

    private function keyWithEatenPlaceholder(string $keyToAdd, bool &$wasEaten = null): string
    {
        $wasEaten = false;
        if (strlen($keyToAdd) !== 0 && $keyToAdd[0] === ':') {
            $wasEaten = true;
            return ($nextSlashPosition = strpos($keyToAdd, '/')) === false ? '' : substr($keyToAdd, $nextSlashPosition);
        }

        return $keyToAdd;
    }

    private function commonKey(string $currentKey, string $keyToAdd, int &$commonPartLength): string
    {
        $commonKey = '';
        $commonPartLength = 0;
        for ($i = 0; $i < min(strlen($currentKey), strlen($keyToAdd)); $i++) {
            if ($currentKey[$i] == $keyToAdd[$i]) {
                $commonPartLength++;
                $commonKey .= $currentKey[$i];
            } else {
                break;
            }
        }

        return $commonKey;
    }

    private function assert(bool $condition): void
    {
        if (!$condition) {
            throw new Exception('Assert failed');
        }
    }

    private function keyAlreadyExists(string $key): bool
    {
        return isset($this->allKeys[$this->maskedKey($key)]);
    }

    private function rememberKey(string $key): void
    {
        $this->allKeys[$this->maskedKey($key)] = true;
    }

    private function maskedKey(string $key): string
    {
        return
            '/'
                .
            implode(
                '/',
                array_map(
                    function (string $keyPart) {
                        return $keyPart[0] === ':' ? ':' : $keyPart;
                    },
                    array_filter(explode('/', $key))
                )
            );
    }
}