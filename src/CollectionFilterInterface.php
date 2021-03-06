<?php

declare(strict_types=1);

namespace TeamA\Collection;

/**
 * Implementations must be immutable.
 *
 * @method string getBaseType() method must return a type name that is covariant for the base type
 *  of the collection corresponding to the filter.
 */
interface CollectionFilterInterface extends CollectionTypeInterface
{
    public function isEmpty(): bool;

    public function checkMatch(object $value): bool;

    /**
     * @return static
     */
    public function and(CollectionFilterInterface $otherFilter): CollectionFilterInterface;

    /**
     * @return static
     */
    public function or(CollectionFilterInterface $otherFilter): CollectionFilterInterface;

    /**
     * @return static
     */
    public function not(): CollectionFilterInterface;
}
