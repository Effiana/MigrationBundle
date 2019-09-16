<?php
declare(strict_types=1);

namespace Effiana\MigrationBundle\Stacktrace;

/**
 * Class ValueAccessor
 * @package Effiana\MigrationBundle\Stacktrace
 */
class ValueAccessor
{
    /**
     * @var array
     */
    protected $cache = array();

    /**
     * @param Node   $node
     * @param string $attribute
     *
     * @return float
     */
    public function sum(Node $node, $attribute): float
    {
        $key = spl_object_hash($node).$attribute;
        
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }
        
        $sum = 0;
        
        foreach ($node->getValues() as $value) {
            if (array_key_exists($attribute, $value)) {
                $sum += $value[$attribute];
            }
        }
        
        foreach ($node as $subNode) {
            $sum += $this->sum($subNode, $attribute);
        }

        return $this->cache[$key] = $sum;
    }
}
