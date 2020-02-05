<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

use TeamA\Collection\CollectionInterface;
use TeamA\Collection\CollectionTrait;

class CollectionIncompleted implements CollectionInterface
{
    use CollectionTrait;
}
