<?php

declare(strict_types=1);

namespace TeamA\Collection;

/**
 * @TODO Immutable implementation of CollectionFilterInterface.
 * @see FilterTrait as ideas source.
 */
trait SorterTrait
{
    use BaseTrait;

    public static function new() : self
    {
        return new static();
    }
}