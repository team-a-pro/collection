<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

class CarCollectionAFilter extends CarCollectionFilter
{
    public function getBaseType(): string
    {
        return CarA::class;
    }
}
