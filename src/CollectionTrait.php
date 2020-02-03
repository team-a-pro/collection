<?php

declare(strict_types=1);

namespace TeamA\Collection;

use InvalidArgumentException;

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
     * @throws InvalidArgumentException
     */
    public function sort(CollectionSorterInterface $sorter): CollectionInterface
    {
        $this->checkType($sorter);

        if ($sorter->isEmpty()) {
            return $this;
        }

        $entities = $this->entities;

        usort($entities, fn($a, $b): int => $sorter->compare($a, $b));

        return $this->createInternal($entities);
    }

    /**
     * @return static
     * @throws InvalidArgumentException
     */
    public function filter(CollectionFilterInterface $filter): CollectionInterface
    {
        return $this->createInternal($this->asArray($filter));
    }

    public function has(CollectionFilterInterface $filter = null): bool
    {
        if ($filter === null || !$this->entities) {
            return (bool) $this->entities;
        }

        $this->checkType($filter);

        foreach ($this->entities as $entity) {
            if ($filter->checkForMatchConditions($entity)) {
                return true;
            }
        }

        return false;
    }

    public function isEmpty(CollectionFilterInterface $filter = null): bool
    {
        if ($filter === null || !$this->entities) {
            return !$this->entities;
        }

        $this->checkType($filter);

        foreach ($this->entities as $entity) {
            if ($filter->checkForMatchConditions($entity)) {
                return false;
            }
        }

        return true;
    }

    public function hasNot(CollectionFilterInterface $filter): bool
    {
        $this->checkType($filter);

        foreach ($this->entities as $entity) {
            if ($filter->checkForMismatchConditions($entity)) {
                return true;
            }
        }

        return false;
    }

    public function count(CollectionFilterInterface $filter = null): int
    {
        if ($filter === null || !$this->entities) {
            return count($this->entities);
        }

        $this->checkType($filter);

        $count = 0;

        foreach ($this->entities as $entity) {
            if ($filter->checkForMatchConditions($entity)) {
                $count++;
            }
        }

        return $count;
    }

    public function asArray(CollectionFilterInterface $filter = null): array
    {
        if ($filter === null || !$this->entities) {
            return $this->entities;
        }

        $this->checkType($filter);

        $entities = [];

        foreach ($this->entities as $entity) {
            if ($filter->checkForMatchConditions($entity)) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    /**
     * @return mixed | null
     */
    public function first(CollectionFilterInterface $filter = null)
    {
        if ($filter === null || !$this->entities) {
            return $this->has() ? current($this->asArray()) : null;
        }

        $this->checkType($filter);

        foreach ($this->entities as $entity) {
            if ($filter->checkForMatchConditions($entity)) {
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
        if ($filter === null || !$this->entities) {
            return $this->has() ? end($this->asArray()) : null;
        }

        $this->checkType($filter);

        $last = null;

        foreach ($this->entities as $entity) {
            if ($filter->checkForMatchConditions($entity)) {
                $last = $entity;
            }
        }

        return $last;
    }

    protected function createInternal(array $entities): CollectionInterface
    {
        return new static($entities);
    }
}
