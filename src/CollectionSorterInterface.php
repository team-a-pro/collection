<?php

declare(strict_types=1);

namespace TeamA\Collection;

/**
 * Implementations must be immutable.
 *
 * @method string getBaseType() method must return a type name that is covariant for the base type
 *  of the collection corresponding to the sorter.
 */
interface CollectionSorterInterface extends CollectionTypeInterface
{
    public function isEmpty(): bool;

    public function compare(object $a, object $b): int;
}
