<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests;

use TeamA\Collection\Tests\Model\Car;
use TeamA\Collection\Tests\Model\CarCollection;
use TeamA\Collection\Tests\Model\CarCollectionFilter;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
    /**
     * @dataProvider carProvider
     */
    public function testCar(
        array                 $carsDefs,
        ? CarCollectionFilter $filter,
        bool                  $expectedHas,
        bool                  $expectedHasNot,
        array                 $expectedFilteredCarsDefs
    )
    {
        $cars = [];
        foreach ($carsDefs as $carDef) { /* @var string[] $carDef */
            $cars[] = new Car(...$carDef);
        }

        $carCollection = new CarCollection($cars);

        $this->assertSame($carCollection->has($filter), $expectedHas);
        $this->assertSame($carCollection->isEmpty($filter), !$expectedHas);
        $this->assertSame($carCollection->hasNot($filter), $expectedHasNot);

        $filteredCarsDefs = [];
        foreach ($carCollection->filter($filter)->asArray() as $car) {
            $filteredCarsDefs[] = $car->toArray();
        }

        $this->assertSame(
            array_values($filteredCarsDefs),
            array_values($expectedFilteredCarsDefs)
        );
    }

    public function carProvider() : array
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

    private function getCars() : array
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
}