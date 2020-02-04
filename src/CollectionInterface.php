<?php

declare(strict_types=1);

namespace TeamA\Collection;

use InvalidArgumentException;

interface CollectionInterface extends CollectionTypeInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function sort(CollectionSorterInterface $sorter): CollectionInterface;

    /**
     * @throws InvalidArgumentException
     */
    public function sortReverse(CollectionSorterInterface $sorter): CollectionInterface;

    /**
     * @throws InvalidArgumentException
     */
    public function filter(CollectionFilterInterface $filter): CollectionInterface;

    /**
     * @throws InvalidArgumentException
     */
    public function filterNotMatched(CollectionFilterInterface $filter): CollectionInterface;

    public function asArray(CollectionFilterInterface $filter = null): array;

    public function asArrayNotMatched(CollectionFilterInterface $filter): array;

    /**
     * @return mixed | null
     */
    public function first(CollectionFilterInterface $filter = null);

    /**
     * @return mixed | null
     */
    public function firstNotMatched(CollectionFilterInterface $filter);

    /**
     * @return mixed | null
     */
    public function last(CollectionFilterInterface $filter = null);

    /**
     * @return mixed | null
     */
    public function lastNotMatched(CollectionFilterInterface $filter);

    public function has(CollectionFilterInterface $filter = null): bool;

    public function isAllMatched(CollectionFilterInterface $filter): ?bool;

    public function isEmpty(CollectionFilterInterface $filter = null): bool;

    public function hasNot(CollectionFilterInterface $filter): bool;

    public function count(CollectionFilterInterface $filter = null): int;

    public function countNotMatched(CollectionFilterInterface $filter = null): int;
}
