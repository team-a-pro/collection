<?php

declare(strict_types=1);

namespace TeamA\Collection;

use TypeError;

trait BaseTrait
{
    public function getBaseType(): string
    {
        throw new TypeError('Base type is not defined');
    }
}
