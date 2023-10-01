<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class MegaFilterServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Nova::serving(function (ServingNova $event): void {

            Nova::script('nova-mega-filter', __DIR__ . '/../dist/js/card.js');
            Nova::style('nova-mega-filter', __DIR__ . '/../dist/css/card.css');

        });
    }
}
