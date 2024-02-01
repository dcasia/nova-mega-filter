<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

use Laravel\Nova\Card;

class MegaFilterCard extends Card
{
    public $width = Card::FULL_WIDTH;

    public function component(): string
    {
        return 'mega-filter-card';
    }
}
