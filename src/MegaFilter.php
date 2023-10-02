<?php

declare(strict_types = 1);

namespace DigitalCreative\MegaFilter;

use Illuminate\Http\Resources\MergeValue;
use Laravel\Nova\Http\Controllers\FilterController;
use Laravel\Nova\Http\Controllers\LensFilterController;
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
        $controller = $this->request()->route()->getControllerClass();

        if (in_array($controller, [ FilterController::class, LensFilterController::class ])) {
            $data = [];
        }

        if ($this->request() instanceof CardRequest) {
            $data = [ new MegaFilterFilterWrapper($this, $data) ];
        }

        parent::__construct($data);
    }

    public function columns(int $columns): self
    {
        return $this->withMeta([ 'columns' => $columns ]);
    }

    private function request(): NovaRequest
    {
        return resolve(NovaRequest::class);
    }
}
