<?php

declare(strict_types=1);

namespace TeamA\Collection;

interface CollectionTypeInterface
{
    public const BASE_TYPE = '';

    public function getBaseType() : string;
}