# collection
TeamA\Collection is a library of interfaces and traits for implementing collections and their filters.

Filters are implemented as objects with a set of modifiers and can be connected using the "AND", "OR" logic.

Provides a check for matching the types included in the collection with the types for which filters are implemented.

Optimized for has, isEmpty, hasNot, and first operations. 

## Requirements

- php >= 7.4

## Install via Composer

`composer require team-a/collection:^1.0.0`

## Examples

For a working example, see tests/Model.

```php
<?php

$collection = new Collection($items); 

$filter = CollectionFilter::new()
    ->withColor('blue')
    ->or(
        CarCollectionFilter::new()->withModel('408')
    )->or(
        CarCollectionFilter::new()->withVendor('VAZ')
    )
;

$hasAtLeastOne = $collection->has($filter);
$hasNoItems    = $collection->isEmpty($filter);
$hasDiscarded  = $collection->hasNot($filter);

$firstItem = $collection->first($item);
$lastItem  = $collection->last($item);

$array = $collection->filter($filter)->asArray();
$count = $collection->count($filter);
```

    

