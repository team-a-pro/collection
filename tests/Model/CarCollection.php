<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

use InvalidArgumentException;
use TeamA\Collection\CollectionFilterInterface;
use TeamA\Collection\CollectionInterface;
use TeamA\Collection\CollectionTrait;

/**
 * @property Car[]       $entities
 * @method   Car[]       asArray(CollectionFilterInterface $filter = null)
 * @method   Car  | null first(CollectionFilterInterface $filter = null)
 * @method   Car  | null last(CollectionFilterInterface $filter = null)
 */
class CarCollection implements CollectionInterface
{
    use CollectionTrait;

    public function getBaseType(): string
    {
        return Car::class;
    }

    /**
     * @param Car[] $cars
     */
    public function __construct(array $cars = [])
    {
        foreach ($cars as $car) {
            if (!$car instanceof Car) {
                throw new InvalidArgumentException();
            }
        }

        $this->entities = $cars;
    }
}
