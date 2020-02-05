<?php

declare(strict_types=1);

namespace TeamA\Collection;

use Closure;

trait SorterTrait
{
    use BaseTrait;

    /**
     * @var Closure[]
     */
    private array $conditions = [];

    public static function new(): self
    {
        return new static();
    }

    public function isEmpty(): bool
    {
        return !$this->conditions;
    }

    public function compare(object $a, object $b): int
    {
        $result = 0;

        foreach ($this->conditions as $condition) {
            $result = $condition($a, $b);

            if ($result) {
                break;
            }
        }

        return (int) $result;
    }

    /**
     * @param Closure[] $conditions
     * @return static
     */
    protected function addConditions(array $conditions): self
    {
        $clone = clone $this;

        foreach ($conditions as $condition) {
            $clone->conditions[] = $condition;
        }

        return $clone;
    }
}
