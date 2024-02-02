<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

class MegaFilterFilterWrapper
{
    public function __construct(
        private MegaFilter $megaFilter,
        private array $filters,
    )
    {
    }

    public function toCard(): MegaFilterCard
    {
        return MegaFilterCard::make()
            ->filters($this->filters)
            ->withMeta($this->megaFilter->meta());
    }
}
