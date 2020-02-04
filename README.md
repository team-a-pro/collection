# collection

[![Coverage Status](https://coveralls.io/repos/github/team-a-pro/collection/badge.svg?branch=master)](https://coveralls.io/github/team-a-pro/collection?branch=master)

TeamA\Collection is a library of interfaces and traits for filtering and sorting collections.

The library solves performance problems, code duplication, and allows you to design contexts for using collections in separate classes.

It is possible to implement sorting and filtering by many criteria.

Sorting and filtering by many criteria is supported. Filters and sorters are implemented as objects with a set of modifiers. Filters can be joined using the "AND", "OR" logic.

Provides a check for matching the types included in the collection with the types for which filters are implemented.

## Requirements

- php >= 7.4

## Install via Composer

`composer require team-a/collection:^1.2`

## Collection methods

* sort
* sortReverse
* filter
* filterNotMatched
* first
* firstNotMatched
* last
* lastNotMatched
* has
* isAllMatched
* isEmpty
* hasNot
* count
* countNotMatched
* asArray
* asArrayNotMatched

## Examples

For a working example, see tests/Model.

```php
<?php

$collection = new CarCollection($cars); 

$collection = $collection->sort(
    CarCollectionSorter::new()
        ->byModel()
        ->byColor()
);


$filter = CollectionFilter::new()
    ->withColor('blue')
    ->or(
        CarCollectionFilter::new()->withModel('408')
    )->or(
        CarCollectionFilter::new()->withVendor('VAZ')
    )
;

$hasAtLeastOneCar = $collection->has($filter);
$hasNoCars        = $collection->isEmpty($filter);
$hasDiscardedCars = $collection->hasNot($filter);

$firstCar = $collection->first($filter);
$lastCar  = $collection->last($filter);

$array = $collection->filter($filter)->asArray();
$count = $collection->count($filter);
```

    

