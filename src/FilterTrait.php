<?php

declare(strict_types=1);

namespace TeamA\Collection;

use Closure;

/**
 * Immutable implementation of CollectionFilterInterface.
 */
trait FilterTrait
{
    use BaseTrait;

    private bool $not = false;

    /**
     * @var Closure[]
     */
    private array $andConditions = [];

    /**
     * @var CollectionFilterInterface[]
     */
    private array $andFilters = [];

    /**
     * @var CollectionFilterInterface[]
     */
    private array $orFilters = [];

    public static function new(): self
    {
        return new static();
    }

    public function isEmpty(): bool
    {
        return !(
            $this->andConditions
            || $this->andFilters
            || $this->orFilters
        );
    }

    public function checkMatch(object $value): bool
    {
        $result = true;

        do {
            foreach ($this->orFilters as $orFilter) {
                if ($orFilter->checkMatch($value)) {
                    $result = true;
                    break 2;
                }
            }

            foreach ($this->andConditions as $condition) {
                if (!$condition($value)) {
                    $result = false;
                    break 2;
                }
            }

            foreach ($this->andFilters as $andFilter) {
                if (!$andFilter->checkMatch($value)) {
                    $result = false;
                    break 2;
                }
            }
        } while (false);

        if ($this->not && !$this->isEmpty()) {
            $result = !$result;
        }

        return $result;
    }

    /**
     * @return static
     */
    public function and(CollectionFilterInterface $filter): CollectionFilterInterface
    {
        $clone = clone $this;
        $clone->andFilters[] = $filter;

        return $clone;
    }

    /**
     * @return static
     */
    public function or(CollectionFilterInterface $filter): CollectionFilterInterface
    {
        $clone = clone $this;
        $clone->orFilters[] = $filter;

        return $clone;
    }

    /**
     * @return static
     */
    public function not(): CollectionFilterInterface
    {
        $clone = clone $this;
        $clone->not = !$clone->not;

        return $clone;
    }

    /**
     * @return static
     */
    protected function withAndCondition(Closure $condition): self
    {
        $clone = clone $this;
        $clone->andConditions[] = $condition;

        return $clone;
    }
}
