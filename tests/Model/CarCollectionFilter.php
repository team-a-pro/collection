<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

use TeamA\Collection\CollectionFilterInterface;
use TeamA\Collection\FilterTrait;

class CarCollectionFilter implements CollectionFilterInterface
{
    use FilterTrait;

    public const BASE_TYPE = Car::class;

    public function withVendor(string $vendor): self
    {
        return $this->withAndCondition(
            fn(Car $car): bool => $car->getVendor() === $vendor
        );
    }

    public function withModel(string $model): self
    {
        return $this->withAndCondition(
            fn(Car $car): bool => $car->getModel() === $model
        );
    }

    public function withColor(string $color): self
    {
        return $this->withAndCondition(
            fn(Car $car): bool => $car->getColor() === $color
        );
    }
}
