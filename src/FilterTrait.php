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

    public static function new() : self
    {
        return new static();
    }

    public function checkForMatchConditions($value): bool
    {
        foreach ($this->orFilters as $orFilter) {
            if ($orFilter->checkForMatchConditions($value)) {
                return true;
            }
        }

        foreach ($this->andConditions as $condition) {
            if (!$condition($value)) {
                return false;
            }
        }

        foreach ($this->andFilters as $andFilter) {
            if (!$andFilter->checkForMatchConditions($value)) {
                return false;
            }
        }

        return true;
    }

    public function checkForMismatchConditions($value): bool
    {
        foreach ($this->andFilters as $andFilter) {
            if ($andFilter->checkForMismatchConditions($value)) {
                return true;
            }
        }

        foreach ($this->andConditions as $condition) {
            if ($condition($value)) {
                return false;
            }
        }

        foreach ($this->orFilters as $orFilter) {
            if (!$orFilter->checkForMismatchConditions($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return static
     */
    public function and(CollectionFilterInterface $filter) : CollectionFilterInterface
    {
        $clone = clone $this;
        $clone->andFilters[] = $filter;

        return $clone;
    }

    /**
     * @return static
     */
    public function or(CollectionFilterInterface $filter) : CollectionFilterInterface
    {
        $clone = clone $this;
        $clone->orFilters[] = $filter;

        return $clone;
    }

    /**
     * @return static
     */
    protected function withAndCondition(Closure $condition) : self
    {
        $clone = clone $this;
        $clone->andConditions[] = $condition;

        return $clone;
    }
}