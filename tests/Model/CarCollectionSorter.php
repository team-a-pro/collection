<?php

namespace TeamA\Collection\Tests\Model;

use TeamA\Collection\CollectionSorterInterface;
use TeamA\Collection\SorterTrait;

class CarCollectionSorter implements CollectionSorterInterface
{
    use SorterTrait;

    public const BASE_TYPE = Car::class;

    public function byVendorDesc(): self
    {
        return $this->addConditions([
            fn(Car $a, Car $b): int => strcmp($b->getVendor(), $a->getVendor())
        ]);
    }

    public function byModel(): self
    {
        return $this->addConditions([
            fn(Car $a, Car $b): int => strcmp($a->getModel(), $b->getModel())
        ]);
    }

    public function byColor(): self
    {
        return $this->addConditions([
            fn(Car $a, Car $b): int => strcmp($a->getColor(), $b->getColor())
        ]);
    }
}
