<?php

namespace Effiana\MigrationBundle\Stacktrace;

/**
 * Class FlattenTraceGraphIterator
 * @package Effiana\MigrationBundle\Stacktrace
 */
class FlattenTraceGraphIterator extends \ArrayIterator implements \RecursiveIterator
{
    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return new static($this->getFlattenChildren($this->current()));
    }

    /**
     * {@inheritdoc}
     */
    public function hasChildren()
    {
        return $this->current() && !empty($this->current()->getNodes());
    }

    /**
     * @param Node $node
     *
     * @return array
     */
    public function getFlattenChildren(Node $node): array
    {
        $flatten = [];
        foreach ($node->getNodes() as $subNode) {
            if (count($subNode->getNodes()) > 1 || isset($subNode->getTrace()['query_source']) || !empty($subNode->getValues())) {
                $flatten[] = $subNode;
            } else {
                $flatten = array_merge($flatten, $this->getFlattenChildren($subNode));
            }
        }

        return $flatten;
    }
}
