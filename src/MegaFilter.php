<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\CardRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;
use Laravel\Nova\Metable;

class MegaFilter extends MergeValue
{
    use Makeable, Metable;

    public function __construct(array $filters)
    {
        parent::__construct(match (true) {
            $this->isCardRequest() => $this->toFilterWrapper($filters),
            default => $this->addFlagToFilters($filters)
        });
    }

    public function columns(int $columns): self
    {
        return $this->withMeta([ 'columns' => $columns ]);
    }

    public function label(string $label): self
    {
        return $this->withMeta([ 'label' => $label ]);
    }

    private function isCardRequest(): bool
    {
        return resolve(NovaRequest::class) instanceof CardRequest;
    }

    private function toFilterWrapper(array $filters): array
    {
        return [
            new MegaFilterFilterWrapper($this, collect($filters)->map(
                fn(Filter $filter) => $filter::class
            )->toArray()),
        ];
    }

    private function addFlagToFilters(array $filters): array
    {
        return collect($filters)->map(
            fn(Filter $filter) => $filter->withMeta([
                'megaFilter' => true,
            ])
        )->all();
    }
}
