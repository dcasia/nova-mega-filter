<?php

namespace DigitalCreative\MegaFilter;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Nova\Card;
use Laravel\Nova\Filters\Filter;

class MegaFilter extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = 'full';

    /**
     * @var array
     */
    public $attributes = [
        'filters' => [],
        'columns' => [],
        'actions' => [],
        'settings' => [

            /**
             * Tailwind width classes: w-full w-1/2 w-1/3 w-1/4 etc.
             */
            'columnsWidth' => 'w-1/4',
            'filtersWidth' => 'w-1/2',

            /**
             * The default state of the main toggle buttons
             */
            'columnsActive' => false,
            'filtersActive' => true,
            'actionsActive' => true,

            /**
             * Labels
             */
            'headerLabel' => 'Menu',
            'columnsLabel' => 'Columns',
            'filtersLabel' => 'Filters',
            'actionsLabel' => 'Actions',
            'columnsSectionTitle' => 'Additional Columns',
            'filtersSectionTitle' => 'Filters',
            'actionsSectionTitle' => 'Actions',
            'columnsResetLinkTitle' => 'Reset Columns',
            'filtersResetLinkTitle' => 'Reset Filters',

        ]
    ];

    public function __construct(array $attributes)
    {
        $this->attributes = $this->array_merge_recursive_distinct($this->attributes, $attributes);
    }

    public function authorizedToSee(Request $request)
    {

        if (parent::authorizedToSee($request)) {

            $this->attributes[ 'filters' ] = array_filter($this->attributes[ 'filters' ], function (Filter $filter) use ($request) {

                return $filter->authorizedToSee($request);

            });

            return true;

        }

        return false;

    }

    private function array_merge_recursive_distinct(array &$array1, array &$array2): array
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {

            if (is_array($value) && isset($merged[ $key ]) && is_array($merged[ $key ])) {

                $merged[ $key ] = $this->array_merge_recursive_distinct($merged[ $key ], $value);

            } else {

                $merged[ $key ] = $value;

            }
        }

        return $merged;
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'mega-filter';
    }

    public function columns(): Collection
    {
        return collect($this->attributes[ 'columns' ]);
    }

    public function actions(): Collection
    {
        return collect($this->attributes[ 'actions' ]);
    }

    public function filters(): Collection
    {
        $columns = $this->columns();

        return $columns
            ->where('permanent', false)
            ->pluck('filters', 'attribute')
            ->each(function ($filters, $attribute) {

                foreach ($filters as $filter) {

                    $filter->withMeta([ 'megaFilterFieldAttribute' => $attribute ]);

                }

            })
            /**
             * Register all permanent columns`s filters as "global filters"
             */
            ->prepend(new MegaFilterColumns)
            ->prepend($columns->where('permanent', true)->pluck('filters'))
            ->prepend($this->attributes[ 'filters' ])
            ->flatten()
            ->map([ $this, 'mockFilter' ]);
    }

    /**
     * Fake every filter component so it doesnt show up on the default filter picker
     *
     * @param Filter $filter
     *
     * @return Filter
     */
    public function mockFilter(Filter $filter): Filter
    {
        $filter->withMeta([ 'component' => 'mega-filter-placeholder' ]);
        $filter->withMeta([ 'originalComponent' => $filter->component ]);

        return $filter;
    }

    public function settings(): Collection
    {
        return collect($this->attributes[ 'settings' ]);
    }

    private function generateCacheKey(): string
    {
        return hash('md5', $this->columns()->pluck('attribute')->sort()->join(''));
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge([
            'settings' => $this->settings(),
            'actions' => $this->actions(),
            'columns' => $this->columns(),
            'filters' => $this->filters(),
            'cacheKey' => $this->generateCacheKey()
        ], parent::jsonSerialize());
    }
}
