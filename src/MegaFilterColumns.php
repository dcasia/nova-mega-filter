<?php

namespace DigitalCreative\MegaFilter;

use App\Nova\Filters\AgeFilter;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class MegaFilterColumns extends Filter
{

    /**
     * The filter's component.
     *
     * @var string
     */

    public function apply(Request $request, $query, $value)
    {

    }

    public function options(Request $request)
    {
    }

}
