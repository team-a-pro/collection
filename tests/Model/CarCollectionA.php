<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

use TeamA\Collection\CollectionFilterInterface;

/**
 * @property CarA[]       $entities
 * @method   CarA[]       asArray(CollectionFilterInterface $filter = null)
 * @method   CarA  | null first(CollectionFilterInterface $filter = null)
 * @method   CarA  | null last(CollectionFilterInterface $filter = null)
 */
class CarCollectionA extends CarCollection
{
    public function getBaseType(): string
    {
        return CarA::class;
    }
}
