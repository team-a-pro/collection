<?php

declare(strict_types=1);

namespace TeamA\Collection;

use TypeError;

/**
 * Phpdoc template for CollectionInterface implementations.
 * Change 'object' to covariant type on copy:
 */

/*
@property object[]            $entities
@method   object[]            asArray(CollectionFilterInterface $filter = null)
@method   object | null       first(CollectionFilterInterface $filter = null)
@method   object | null       last(CollectionFilterInterface $filter = null)
*/
trait CollectionTrait
{
    use BaseTrait;

    protected array $entities = [];

    /**
     * @return static
     * @throws TypeError
     */
    public function sort(CollectionSorterInterface $sorter): CollectionInterface
    {
        return $this->sortInternal($sorter);
    }

    /**
     * @return static
     * @throws TypeError
     */
    public function sortReverse(CollectionSorterInterface $sorter): CollectionInterface
    {
        return $this->sortInternal($sorter, true);
    }

    /**
     * @return static
     * @throws TypeError
     */
    public function filter(CollectionFilterInterface $filter): CollectionInterface
    {
        if ($filter === null) {
            return $this;
        }

        if ($filter->isEmpty()) {
            $this->checkType($filter);
            return $this;
        }

        return $this->createInternal(
            $this->asArray($filter)
        );
    }

    /**
     * @return static
     * @throws TypeError
     */
    public function filterNotMatched(CollectionFilterInterface $filter): CollectionInterface
    {
        if ($filter === null) {
            return $this;
        }

        if ($filter->isEmpty()) {
            $this->checkType($filter);
            return $this;
        }

        return $this->createInternal(
            $this->asArrayNotMatched($filter)
        );
    }

    public function has(CollectionFilterInterface $filter = null): bool
    {
        if ($filter !== null) {
            $this->checkType($filter);
        }

        if ($filter === null || $filter->isEmpty()) {
            return (bool) $this->entities;
        }

        if (!$this->entities) {
            return false;
        }

        foreach ($this->entities as $entity) {
            if ($filter->checkMatch($entity)) {
                return true;
            }
        }

        return false;
    }

    public function isAllMatched(CollectionFilterInterface $filter): ?bool
    {
        if (!$this->entities) {
            $this->checkType($filter);
            return null;
        }

        return !$this->hasNot($filter);
    }

    public function isEmpty(CollectionFilterInterface $filter = null): bool
    {
        if ($filter !== null) {
            $this->checkType($filter);
        }

        if ($filter === null || $filter->isEmpty()) {
            return !$this->entities;
        }

        if (!$this->entities) {
            return !$this->entities;
        }

        foreach ($this->entities as $entity) {
            if ($filter->checkMatch($entity)) {
                return false;
            }
        }

        return true;
    }

    public function hasNot(CollectionFilterInterface $filter): bool
    {
        $this->checkType($filter);

        foreach ($this->entities as $entity) {
            if (!$filter->checkMatch($entity)) {
                return true;
            }
        }

        return false;
    }

    public function count(CollectionFilterInterface $filter = null): int
    {
        return $this->countInternal($filter);
    }

    public function countNotMatched(CollectionFilterInterface $filter = null): int
    {
        return $this->countInternal($filter, true);
    }

    public function asArray(CollectionFilterInterface $filter = null): array
    {
        return $this->toArray($filter);
    }

    public function asArrayNotMatched(CollectionFilterInterface $filter): array
    {
        return $this->toArray($filter, true);
    }

    /**
     * @return mixed | null
     */
    public function first(CollectionFilterInterface $filter = null)
    {
        if ($filter !== null) {
            $this->checkType($filter);
        }

        if (!$this->entities) {
            return null;
        }

        if ($filter === null || $filter->isEmpty()) {
            $entities = $this->entities;
            return current($entities);
        }

        foreach ($this->entities as $entity) {
            if ($filter->checkMatch($entity)) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @return mixed | null
     */
    public function firstNotMatched(CollectionFilterInterface $filter)
    {
        $this->checkType($filter);

        if ($filter->isEmpty()) {
            return null;
        }

        foreach ($this->entities as $entity) {
            if (!$filter->checkMatch($entity)) {
                return $entity;
            }
        }

        return null;
    }

    /**
     * @return mixed | null
     */
    public function last(CollectionFilterInterface $filter = null)
    {
        if ($filter !== null) {
            $this->checkType($filter);
        }

        if (!$this->entities) {
            return null;
        }

        if ($filter === null || $filter->isEmpty()) {
            $entities = $this->entities;
            return end($entities);
        }

        $last = null;

        foreach ($this->entities as $entity) {
            if ($filter->checkMatch($entity)) {
                $last = $entity;
            }
        }

        return $last;
    }

    /**
     * @return mixed | null
     */
    public function lastNotMatched(CollectionFilterInterface $filter)
    {
        $this->checkType($filter);

        if ($filter->isEmpty()) {
            return null;
        }

        $last = null;

        foreach ($this->entities as $entity) {
            if (!$filter->checkMatch($entity)) {
                $last = $entity;
            }
        }

        return $last;
    }

    protected function createInternal(array $entities): CollectionInterface
    {
        return new static($entities);
    }

    /**
     * @throws TypeError
     */
    protected function sortInternal(CollectionSorterInterface $sorter, bool $reverse = false): CollectionInterface
    {
        $this->checkType($sorter);

        if ($sorter->isEmpty()) {
            return $this;
        }

        $entities = $this->entities;

        $multiplier = $reverse ? -1 : 1;

        usort($entities, fn($a, $b): int => $sorter->compare($a, $b) * $multiplier);

        return $this->createInternal($entities);
    }

    /**
     * @throws TypeError
     */
    protected function toArray(CollectionFilterInterface $filter = null, bool $invertFilter = false): array
    {
        if ($filter !== null) {
            $this->checkType($filter);
        }

        if ($filter === null || $filter->isEmpty()) {
            return $this->entities;
        }

        if (!$this->entities) {
            return $this->entities;
        }

        $entities = [];

        foreach ($this->entities as $entity) {
            if (
                $invertFilter
                    ? !$filter->checkMatch($entity)
                    : $filter->checkMatch($entity)
            ) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * @throws TypeError
     */
    protected function countInternal(CollectionFilterInterface $filter = null, bool $notMatched = false): int
    {
        if ($filter === null) {
            return count($this->entities);
        }

        $this->checkType($filter);

        if (!$this->entities) {
            return 0;
        }

        if ($filter->isEmpty()) {
            return count($this->entities);
        }

        $count = 0;

        foreach ($this->entities as $entity) {
            if (
                $notMatched
                    ? !$filter->checkMatch($entity)
                    : $filter->checkMatch($entity)
            ) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @throws TypeError
     */
    protected function checkType(CollectionTypeInterface $type): void
    {
        if (!is_a($this->getBaseType(), $type->getBaseType(), true)) {
            throw new TypeError('Collection item base type must be an contrvariant of filter/sorter item base type.');
        }
    }
}
