<?php

declare(strict_types=1);

namespace TeamA\Collection\Tests\Model;

class Car
{
    private string $vendor;
    private string $model;
    private string $color;

    public function __construct(string $vendor, string $model, string $color)
    {
        $this->vendor = $vendor;
        $this->model  = $model;
        $this->color  = $color;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [
            $this->vendor,
            $this->model,
            $this->color
        ];
    }
}
