<?php
declare(strict_types=0);
namespace Effiana\MigrationBundle\Stacktrace;

/**
 * Class Node
 * @package Effiana\MigrationBundle\Stacktrace
 */
class Node implements \RecursiveIterator
{
    /**
     * @var array
     */
    protected $values = array();

    /**
     * @var array
     */
    protected $trace;

    /**
     * @var Node
     */
    protected $parent;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Node[]
     */
    public $nodes = array();

    /**
     * @param array $trace
     * @param Node  $parent
     */
    public function __construct(array $trace, Node $parent = null)
    {
        $this->trace = $trace;
        $this->parent = $parent;
        $this->id = $this->createId();
        if ($parent) {
            $parent->nodes[$this->getId()] = $this;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return current($this->nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return key($this->nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        next($this->nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        reset($this->nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return $this->current() !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @return int
     */
    protected function createId(): int
    {
        static $id = 0;
        
        return ++$id;
    }

    /**
     * @param array $trace
     *
     * @return Node
     */
    public function push($trace): Node
    {
        foreach ($this as $node) {
            if ($node->getTrace() === $trace) {
                return $node;
            }
        }
        
        $node = new static($trace, $this);
        return $node;
    }

    /**
     * @return Node
     */
    public function getParent(): ?Node
    {
        return $this->parent;
    }

    /**
     * @return Node
     */
    public function getRoot(): Node
    {
        return $this->getParent() ? $this->getParent()->getRoot() : $this;
    }

    /**
     * @return string
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function hasChildren(): bool
    {
        return $this->current() && !empty($this->current()->getNodes());
    }

    /**
     * @return \RecursiveIterator
     */
    public function getChildren()
    {
        return $this->current();
    }

    /**
     * @param mixed $value
     */
    public function addValue($value): void
    {
        $this->values[] = $value;
    }

    /**
     * @param bool $recursive
     * @return mixed
     */
    public function getValues($recursive = false)
    {
        $values = $this->values;
        if ($recursive) {
            foreach ($this as $node) {
                $values = array_merge($values, $node->getValues(true));
            }
        }
        return $values;
    }

    /**
     * @return bool
     */
    public function containsBranch(): bool
    {
        if (count($this->nodes)>1) {
            return true;
        }

        foreach ($this as $node) {
            if ($node->containsBranch()) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array
     */
    public function getTrace(): array
    {
        return $this->trace;
    }
}
