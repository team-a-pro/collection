<?php

declare(strict_types=1);

namespace TeamA\Collection;

use InvalidArgumentException;

interface CollectionInterface extends CollectionTypeInterface
{
    /**
     * @throws InvalidArgumentException
     */
    public function sort(CollectionSorterInterface $sorter) : CollectionInterface;

    /**
     * @throws InvalidArgumentException
     */
    public function filter(CollectionFilterInterface $filter) : CollectionInterface;

    public function asArray(CollectionFilterInterface $filter = null) : array;

    /**
     * @return mixed | null
     */
    public function first(CollectionFilterInterface $filter = null);

    /**
     * @return mixed | null
     */
    public function last(CollectionFilterInterface $filter = null);

    public function has(CollectionFilterInterface $filter = null) : bool;

    public function isEmpty(CollectionFilterInterface $filter = null) : bool;

    public function hasNot(CollectionFilterInterface $filter) : bool;

    public function count(CollectionFilterInterface $filter = null) : int;
}