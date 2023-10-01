<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Resource;

/**
 * @mixin Resource|Lens
 */
trait MegaFilterTrait
{
    public function availableCards(NovaRequest $request): Collection
    {
        $cards = $this->resolveFilters($request)
            ->whereInstanceOf(MegaFilterFilterWrapper::class)
            ->map(fn (MegaFilterFilterWrapper $filter) => $filter->toCard());

        return parent::availableCards($request)->merge($cards);
    }
}
