<?php

declare(strict_types=1);

namespace TeamA\Collection;

interface CollectionTypeInterface
{
    public function getBaseType() : string;
}