<?php

declare(strict_types=1);

namespace TeamA\Collection;

use InvalidArgumentException;

trait BaseTrait
{
    public function getBaseType(): string
    {
        return static::BASE_TYPE;
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function checkType(CollectionTypeInterface $type): void
    {
        if (!is_a($this->getBaseType(), $type->getBaseType(), true)) {
            throw new InvalidArgumentException();
        }
    }
}
