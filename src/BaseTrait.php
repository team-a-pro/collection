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

    /**
     * @throws TypeError
     */
    protected function checkType(CollectionTypeInterface $type): void
    {
        if (!is_a($this->getBaseType(), $type->getBaseType(), true)) {
            throw new TypeError('Incompatible base types');
        }
    }
}
