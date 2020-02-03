<?php

declare(strict_types=1);

namespace TeamA\Collection;

/**
 * Implementations must be immutable.
 *
 * @method string getBaseType() method must return a type that is compensatory for the base type of the collection corresponding to the sorter.
 */
interface CollectionSorterInterface extends CollectionTypeInterface
{
    public function compare($a, $b) : int;
}