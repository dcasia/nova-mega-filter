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
    use Makeable;
    use Metable;

    public function __construct(array $data)
    {
        if ($this->request() instanceof CardRequest) {
            $data = [
                new MegaFilterFilterWrapper(
                    $this, collect($data)->map(fn(Filter $filter) => $filter::class)->toArray()
                )
            ];
        } else {

            $data = collect($data)->map(fn(Filter $filter) => $filter->withMeta([
                'megaFilter' => true,
            ]))->all();

        }

        parent::__construct($data);
    }

    public function columns(int $columns): self
    {
        return $this->withMeta([ 'columns' => $columns ]);
    }

    public function label(string $label): self
    {
        return $this->withMeta([ 'label' => $label ]);
    }

    private function request(): NovaRequest
    {
        return resolve(NovaRequest::class);
    }
}
