<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests;

use Closure;
use TeamA\Collection\Tests\Model\Car;
use TeamA\Collection\Tests\Model\CarCollection;
use TeamA\Collection\Tests\Model\CarCollectionFilter;
use PHPUnit\Framework\TestCase;
use TeamA\Collection\Tests\Model\CarCollectionSorter;

class FilterTest extends TestCase
{
    /**
     * @dataProvider filterCarProvider
     * @dataProvider filterEmptyCarsProvider
     */
    public function testFilterCar(
        array $carsDefs,
        ?CarCollectionFilter $filter,
        bool $expectedHas,
        bool $expectedHasNot,
        array $expectedFilteredCarsDefs
    ) {
        $expectedInvertedFilteredDefs = $this->defsDiff($carsDefs, $expectedFilteredCarsDefs);

        $carCollection = $this->getCarCollection($carsDefs);

        $this->assertSame($carCollection->has($filter), $expectedHas);
        $this->assertSame($carCollection->isEmpty($filter), !$expectedHas);
        $this->assertSame($carCollection->count($filter), count($expectedFilteredCarsDefs));

        $this->assertSame(
            $this->carToDef($carCollection->first($filter)) ?? false,
            current($expectedFilteredCarsDefs)
        );

        $this->assertSame(
            $this->carToDef($carCollection->last($filter)) ?? false,
            end($expectedFilteredCarsDefs)
        );

        if ($filter) {
            $this->assertSame($carCollection->hasNot($filter), $expectedHasNot);
            $this->assertSame($carCollection->isAllMatched($filter), $carsDefs ? !$expectedHasNot : null);
            $this->assertSame($carCollection->countNotMatched($filter), count($expectedInvertedFilteredDefs));

            $this->assertSame(
                $this->carToDef($carCollection->firstNotMatched($filter)) ?? false,
                current($expectedInvertedFilteredDefs)
            );

            $this->assertSame(
                $this->carToDef($carCollection->lastNotMatched($filter)) ?? false,
                end($expectedInvertedFilteredDefs)
            );

            $filteredDefs = $this->carCollectionToDefs(
                $carCollection->filter($filter)
            );

            $this->assertSame(
                array_values($filteredDefs),
                array_values($expectedFilteredCarsDefs)
            );

            $invertedFilteredDefs = $this->carCollectionToDefs(
                $carCollection->filterNotMatched($filter)
            );

            $this->sortDefs($invertedFilteredDefs);
            $this->sortDefs($expectedInvertedFilteredDefs);

            $this->assertSame(
                array_values($invertedFilteredDefs),
                array_values($expectedInvertedFilteredDefs)
            );
        }
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
            $this->getCarCollection($carsDefs)->sort($sorter)
        );

        $reverseSortedDefs = $this->carCollectionToDefs(
            $this->getCarCollection($carsDefs)->sortReverse($sorter)
        );

        $this->assertSame(
            array_values($sortedDefs),
            array_values($expectedSortedCarsDef)
        );

        $this->assertSame(
            array_values($reverseSortedDefs),
            array_values(
                $sorter->isEmpty() ? $expectedSortedCarsDef : array_reverse($expectedSortedCarsDef)
            )
        );
    }

    public function filterEmptyCarsProvider(): array
    {
        $epmtyTests = [];
        foreach ($this->filterCarProvider() as $test) {
            $test[0] = [];
            $test[2] = false;
            $test[3] = false;
            $test[4] = [];

            $epmtyTests[] = $test;
        }

        return $epmtyTests;
    }

    public function filterCarProvider(): array
    {
        $cars = $this->getCars();

        return [
            0 => [
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
            1 => [
                $cars,
                CarCollectionFilter::new()
                    ->withModel('2102')
                ,
                false, true,
                []
            ],
            2 => [
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
            3 => [
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
            4 => [
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
            5 => [
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
            6 => [
                $cars,
                null,
                true, false,
                $cars
            ],
        ];
    }

    public function sortCarProvider(): array
    {
        $cars = $this->getCars();

        return [
            'emtpy sorter' => [
                $cars,
                CarCollectionSorter::new(),
                $cars
            ],
            'complex sorter' => [
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

    private function getCarCollection(array $carsDefs): CarCollection
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
            $carsDefs[] = $this->carToDef($car);
        }

        return $carsDefs;
    }

    private function carToDef(?Car $car): ?array
    {
        return $car ? $car->toArray() : null;
    }

    private function defsDiff(array $a, array $b): array
    {
        return array_map(
            $this->getStringToDefFn(),
            array_diff(
                array_map($this->getDefToStringFn(), $a),
                array_map($this->getDefToStringFn(), $b)
            )
        );
    }

    private function sortDefs(array &$defs): void
    {
        $defsAsStrings = array_map($this->getDefToStringFn(), $defs);
        sort($defsAsStrings);
        $defs = array_map($this->getStringToDefFn(), $defsAsStrings);
    }

    private function getDefToStringFn(): Closure
    {
        return fn(array $array): string => join('|', $array);
    }

    private function getStringToDefFn(): Closure
    {
        return fn(string $string): array => explode('|', $string);
    }
}
