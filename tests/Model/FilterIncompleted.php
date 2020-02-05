<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

use TeamA\Collection\CollectionFilterInterface;
use TeamA\Collection\FilterTrait;

class FilterIncompleted implements CollectionFilterInterface
{
    use FilterTrait;
}
