<?php

declare(strict_types=1);

namespace TeamA\Collection;

/**
 * Implementations must be immutable.
 *
 * @method string getBaseType() method must return a type that is compensatory for the base type of the
 *  collection corresponding to the filter.
 */
interface CollectionFilterInterface extends CollectionTypeInterface
{
    public function checkForMatchConditions(object $value): bool;

    public function checkForMismatchConditions(object $value): bool;

    /**
     * @return static
     */
    public function and(CollectionFilterInterface $otherFilter): CollectionFilterInterface;

    /**
     * @return static
     */
    public function or(CollectionFilterInterface $otherFilter): CollectionFilterInterface;
}
