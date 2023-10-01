<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

class MegaFilterFilterWrapper
{
    public function __construct(
        public array $filters,
    )
    {
    }

    public function toCard(): MegaFilterCard
    {
        return MegaFilterCard::make()->addFilters($this->filters);
    }
}
