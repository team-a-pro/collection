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
     * @dataProvider emptyFilterCarsProvider
     */
    public function testFilterCar(
        array $carsDefs,
        ?CarCollectionFilter $filter,
        bool $expectedHas,
        bool $expectedHasNot,
        array $expectedFilteredCarsDefs
    ) {
        $emptyFilter = $filter === null || $filter->isEmpty();

        $expectedInvertedFilteredDefs = $emptyFilter
            ? $carsDefs
            : $this->defsDiff($carsDefs, $expectedFilteredCarsDefs)
        ;

        $carCollection = $this->getCarCollection($carsDefs);

        $this->assertSame($expectedHas, $carCollection->has($filter));
        $this->assertSame(!$expectedHas, $carCollection->isEmpty($filter));
        $this->assertSame(count($expectedFilteredCarsDefs), $carCollection->count($filter));

        $this->assertSame(
            current($expectedFilteredCarsDefs),
            $this->carToDef($carCollection->first($filter)) ?? false
        );

        $this->assertSame(
            end($expectedFilteredCarsDefs),
            $this->carToDef($carCollection->last($filter)) ?? false
        );

        if ($filter) {
            $this->assertSame($expectedHasNot, $carCollection->hasNot($filter));
            $this->assertSame($carsDefs ? !$expectedHasNot : null, $carCollection->isAllMatched($filter));
            $this->assertSame(count($expectedInvertedFilteredDefs), $carCollection->countNotMatched($filter));

            $this->assertSame(
                $emptyFilter ? null : (current($expectedInvertedFilteredDefs) ?: null),
                $this->carToDef($carCollection->firstNotMatched($filter))
            );

            $this->assertSame(
                $emptyFilter ? null : (end($expectedInvertedFilteredDefs) ?: null),
                $this->carToDef($carCollection->lastNotMatched($filter))
            );

            $filteredDefs = $this->carCollectionToDefs(
                $carCollection->filter($filter)
            );

            $this->assertSame(
                array_values($expectedFilteredCarsDefs),
                array_values($filteredDefs)
            );

            $invertedFilteredDefs = $this->carCollectionToDefs(
                $carCollection->filterNotMatched($filter)
            );

            $this->sortDefs($invertedFilteredDefs);
            $this->sortDefs($expectedInvertedFilteredDefs);

            $this->assertSame(
                array_values($expectedInvertedFilteredDefs),
                array_values($invertedFilteredDefs)
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
            array_values($expectedSortedCarsDef),
            array_values($sortedDefs)
        );

        $this->assertSame(
            array_values(
                $sorter->isEmpty() ? $expectedSortedCarsDef : array_reverse($expectedSortedCarsDef)
            ),
            array_values($reverseSortedDefs)
        );
    }

    public function emptyFilterCarsProvider(): array
    {
        $emptyFilterTests = [];
        foreach ($this->filterCarProvider() as $i => $test) {
            /**
             * No filters
             */
            $test[1] = null;
            $test[2] = (bool) $test[0];
            $test[3] = false;
            $test[4] = $test[0];
            $emptyFilterTests['no filter ' . $i] = $test;

            /**
             * Empty filter
             */
            $test[1] = CarCollectionFilter::new();
            $emptyFilterTests['empty filter ' . $i] = $test;
        }

        return $emptyFilterTests;
    }

    public function filterEmptyCarsProvider(): array
    {
        $epmtyTests = [];
        foreach ($this->filterCarProvider() as $i => $test) {
            $test[0] = [];
            $test[2] = false;
            $test[3] = false;
            $test[4] = [];

            $epmtyTests['empty cars ' . $i] = $test;
        }

        return $epmtyTests;
    }

    public function filterCarProvider(): array
    {
        $cars = $this->getCars();

        return [
            '#1' => [
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
            '#2' => [
                $cars,
                CarCollectionFilter::new()
                    ->withModel('2102')
                ,
                false, true,
                []
            ],
            '#3' => [
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
            '#4' => [
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
            '#5' => [
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
            '#6' => [
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
            '#7' => [
                $cars,
                null,
                true, false,
                $cars
            ],
            '#8' => [
                $cars,
                CarCollectionFilter::new()
                    ->withColor('blue')
                    ->and(
                        CarCollectionFilter::new()->withModel('2105')
                    )
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
                ]
            ],
            '#9' => [
                $cars,
                CarCollectionFilter::new()
                    ->withColor('blue')
                    ->and(
                        CarCollectionFilter::new()->withModel('2105')
                    )
                    ->or(
                        CarCollectionFilter::new()->withModel('408')
                    )->or(
                        CarCollectionFilter::new()->withVendor('VAZ')->and(
                            CarCollectionFilter::new()->withModel('2101')->not()
                        )
                    )
                ,
                true, true,
                [
                    ['VAZ', '2105', 'white'],
                    ['VAZ', '2105', 'blue'],
                    ['VAZ', '2105', 'red'],

                    ['VAZ', '2109', 'white'],
                    ['VAZ', '2109', 'blue'],
                    ['VAZ', '2109', 'red'],

                    ['Moskvich', '408', 'white'],
                    ['Moskvich', '408', 'blue'],
                    ['Moskvich', '408', 'red'],
                ]
            ],
            '#10' => [
                [
                    $cars['VAZ 2101 white'],
                    $cars['VAZ 2101 blue'],
                    $cars['VAZ 2101 red']
                ],
                CarCollectionFilter::new()
                    ->withModel('2101')
                    ->and(
                        CarCollectionFilter::new()->not()->withColor('blue')
                    )
                ,
                true, true,
                [
                    ['VAZ', '2101', 'white'],
                    ['VAZ', '2101', 'red'],
                ]
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
