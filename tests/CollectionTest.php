<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests;

use TeamA\Collection\Tests\Model\Car;
use TeamA\Collection\Tests\Model\CarCollection;
use TeamA\Collection\Tests\Model\CarCollectionFilter;
use PHPUnit\Framework\TestCase;
use TeamA\Collection\Tests\Model\CarCollectionSorter;

class FilterTest extends TestCase
{
    /**
     * @dataProvider filterCarProvider
     */
    public function testFilterCar(
        array $carsDefs,
        ?CarCollectionFilter $filter,
        bool $expectedHas,
        bool $expectedHasNot,
        array $expectedFilteredCarsDefs
    ) {
        $carCollection = $this->getCartCollection($carsDefs);

        $this->assertSame($carCollection->has($filter), $expectedHas);
        $this->assertSame($carCollection->isEmpty($filter), !$expectedHas);
        $this->assertSame($carCollection->hasNot($filter), $expectedHasNot);

        $filteredDefs = $this->carCollectionToDefs(
            $carCollection->filter($filter)
        );

        $this->assertSame(
            array_values($filteredDefs),
            array_values($expectedFilteredCarsDefs)
        );
    }

    /**
     * @dataProvider sortCarProvider
     */
    public function testSortCar(
        array $carsDefs,
        CarCollectionSorter $sorter,
        array $expectedSortedCarsDef
    ) {
        $sortedDefs = $this->carCollectionToDefs(
            $this->getCartCollection($carsDefs)->sort($sorter)
        );

        $this->assertSame(
            array_values($sortedDefs),
            array_values($expectedSortedCarsDef)
        );
    }

    public function filterCarProvider(): array
    {
        $cars = $this->getCars();

        return [
            [
                $cars,
                CarCollectionFilter::new()
                    ->withColor('white')
                    ->withVendor('VAZ')
                    ->withModel('2101')
                ,
                true, true,
                [
                    ['VAZ', '2101', 'white']
                ]
            ],
            [
                $cars,
                CarCollectionFilter::new()
                    ->withModel('2102')
                ,
                false, true,
                []
            ],
            [
                [
                    $cars['VAZ 2101 white'],
                    $cars['VAZ 2101 blue'],
                    $cars['VAZ 2101 red']
                ],
                CarCollectionFilter::new()
                    ->withModel('2101')
                ,
                true, false,
                [
                    ['VAZ', '2101', 'white'],
                    ['VAZ', '2101', 'blue'],
                    ['VAZ', '2101', 'red'],
                ]
            ],
            [
                $cars,
                CarCollectionFilter::new()
                    ->withColor('blue')
                    ->and(
                        CarCollectionFilter::new()->withVendor('VAZ')
                    )
                ,
                true, true,
                [
                    ['VAZ', '2101', 'blue'],
                    ['VAZ', '2105', 'blue'],
                    ['VAZ', '2109', 'blue'],
                ]
            ],
            [
                $cars,
                CarCollectionFilter::new()
                    ->withColor('blue')
                    ->or(
                        CarCollectionFilter::new()->withVendor('VAZ')
                    )
                ,
                true, true,
                [
                    ['VAZ', '2101', 'white'],
                    ['VAZ', '2101', 'blue'],
                    ['VAZ', '2101', 'red'],

                    ['VAZ', '2105', 'white'],
                    ['VAZ', '2105', 'blue'],
                    ['VAZ', '2105', 'red'],

                    ['VAZ', '2109', 'white'],
                    ['VAZ', '2109', 'blue'],
                    ['VAZ', '2109', 'red'],

                    ['Moskvich', '408', 'blue'],
                    ['Moskvich', '412', 'blue'],
                    ['Moskvich', '427', 'blue'],

                    ['ZIS', '101', 'blue'],
                    ['ZIS', '110', 'blue'],
                    ['ZIS', '115', 'blue'],
                ]
            ],
            [
                $cars,
                CarCollectionFilter::new()
                    ->withColor('blue')
                    ->or(
                        CarCollectionFilter::new()->withModel('408')
                    )->or(
                        CarCollectionFilter::new()->withVendor('VAZ')
                    )
                ,
                true, true,
                [
                    ['VAZ', '2101', 'white'],
                    ['VAZ', '2101', 'blue'],
                    ['VAZ', '2101', 'red'],

                    ['VAZ', '2105', 'white'],
                    ['VAZ', '2105', 'blue'],
                    ['VAZ', '2105', 'red'],

                    ['VAZ', '2109', 'white'],
                    ['VAZ', '2109', 'blue'],
                    ['VAZ', '2109', 'red'],


                    ['Moskvich', '408', 'white'],
                    ['Moskvich', '408', 'blue'],
                    ['Moskvich', '408', 'red'],

                    ['Moskvich', '412', 'blue'],
                    ['Moskvich', '427', 'blue'],

                    ['ZIS', '101', 'blue'],
                    ['ZIS', '110', 'blue'],
                    ['ZIS', '115', 'blue'],
                ]
            ],
        ];
    }

    public function sortCarProvider(): array
    {
        $cars = $this->getCars();

        return [
            [
                $cars,
                CarCollectionSorter::new(),
                $cars
            ],
            [
                $cars,
                CarCollectionSorter::new()
                    ->byColor()
                    ->byVendorDesc()
                    ->byModel()
                ,
                [
                    ['ZIS', '101', 'blue'],
                    ['ZIS', '110', 'blue'],
                    ['ZIS', '115', 'blue'],

                    ['VAZ', '2101', 'blue'],
                    ['VAZ', '2105', 'blue'],
                    ['VAZ', '2109', 'blue'],

                    ['Moskvich', '408', 'blue'],
                    ['Moskvich', '412', 'blue'],
                    ['Moskvich', '427', 'blue'],

                    ['ZIS', '101', 'red'],
                    ['ZIS', '110', 'red'],
                    ['ZIS', '115', 'red'],

                    ['VAZ', '2101', 'red'],
                    ['VAZ', '2105', 'red'],
                    ['VAZ', '2109', 'red'],

                    ['Moskvich', '408', 'red'],
                    ['Moskvich', '412', 'red'],
                    ['Moskvich', '427', 'red'],

                    ['ZIS', '101', 'white'],
                    ['ZIS', '110', 'white'],
                    ['ZIS', '115', 'white'],

                    ['VAZ', '2101', 'white'],
                    ['VAZ', '2105', 'white'],
                    ['VAZ', '2109', 'white'],

                    ['Moskvich', '408', 'white'],
                    ['Moskvich', '412', 'white'],
                    ['Moskvich', '427', 'white'],
                ]
            ]
        ];
    }

    private function getCars(): array
    {
        return [
            'VAZ 2101 white' => ['VAZ', '2101', 'white'],
            'VAZ 2101 blue'  => ['VAZ', '2101', 'blue'],
            'VAZ 2101 red'   => ['VAZ', '2101', 'red'],

            'VAZ 2105 white' => ['VAZ', '2105', 'white'],
            'VAZ 2105 blue'  => ['VAZ', '2105', 'blue'],
            'VAZ 2105 red'   => ['VAZ', '2105', 'red'],

            'VAZ 2109 white' => ['VAZ', '2109', 'white'],
            'VAZ 2109 blue'  => ['VAZ', '2109', 'blue'],
            'VAZ 2109 red'   => ['VAZ', '2109', 'red'],

            'Moskvich 408 white' => ['Moskvich', '408', 'white'],
            'Moskvich 408 blue'  => ['Moskvich', '408', 'blue'],
            'Moskvich 408 red'   => ['Moskvich', '408', 'red'],

            'Moskvich 412 white' => ['Moskvich', '412', 'white'],
            'Moskvich 412 blue'  => ['Moskvich', '412', 'blue'],
            'Moskvich 412 red'   => ['Moskvich', '412', 'red'],

            'Moskvich 427 white' => ['Moskvich', '427', 'white'],
            'Moskvich 427 blue'  => ['Moskvich', '427', 'blue'],
            'Moskvich 427 red'   => ['Moskvich', '427', 'red'],

            'ZIS 101 white' => ['ZIS', '101', 'white'],
            'ZIS 101 blue'  => ['ZIS', '101', 'blue'],
            'ZIS 101 red'   => ['ZIS', '101', 'red'],

            'ZIS 110 white' => ['ZIS', '110', 'white'],
            'ZIS 110 blue'  => ['ZIS', '110', 'blue'],
            'ZIS 110 red'   => ['ZIS', '110', 'red'],

            'ZIS 115 white' => ['ZIS', '115', 'white'],
            'ZIS 115 blue'  => ['ZIS', '115', 'blue'],
            'ZIS 115 red'   => ['ZIS', '115', 'red'],
        ];
    }

    private function getCartCollection(array $carsDefs): CarCollection
    {
        $cars = [];
        foreach ($carsDefs as $carDef) { /* @var string[] $carDef */
            $cars[] = new Car(...$carDef);
        }

        return new CarCollection($cars);
    }

    private function carCollectionToDefs(CarCollection $carCollection): array
    {
        $carsDefs = [];
        foreach ($carCollection->asArray() as $car) {
            $carsDefs[] = $car->toArray();
        }

        return $carsDefs;
    }
}
