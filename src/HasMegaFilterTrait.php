<?php

namespace DigitalCreative\MegaFilter;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\FilterDecoder;
use Laravel\Nova\Http\Controllers\ActionController;
use Laravel\Nova\Http\Controllers\FilterController;
use Laravel\Nova\Http\Controllers\ResourceCountController;
use Laravel\Nova\Http\Controllers\ResourceIndexController;
use Laravel\Nova\Http\Controllers\ResourceShowController;
use Laravel\Nova\Http\Requests\NovaRequest;

trait HasMegaFilterTrait
{
    /**
     * Get the actions for the given request.
     *
     * @param NovaRequest $request
     *
     * @return Collection
     */
    public function resolveActions(NovaRequest $request)
    {

        if ($this->shouldApplyMegaFilter($request) && $card = $this->getMegaFilterCard($request)) {

            return parent::resolveActions($request)->merge($card->actions());

        }

        return parent::resolveActions($request);

    }

    /**
     * Get the filters for the given request.
     *
     * @param NovaRequest $request
     *
     * @return Collection
     */
    public function resolveFilters(NovaRequest $request)
    {
        if ($this->shouldApplyMegaFilter($request) && $card = $this->getMegaFilterCard($request)) {
            $filterColumns = $this->getFilterColumns();

            if ($filterColumns) {
                session(['mega-filter-columns' => $filterColumns]);
            }

            return parent::resolveFilters($request)->merge($card->filters());

        }

        return parent::resolveFilters($request);

    }

    public function availableFields(NovaRequest $request)
    {

        $fields = parent::availableFields($request);
        $card = $this->getMegaFilterCard($request);

        if ($this->shouldApplyMegaFilter($request) === false || blank($card)) {

            return $fields;

        }

        $fieldsToShow = $this->getFilterState($request, $card);

        return $fields->filter(static function ($field) use ($fieldsToShow) {

            if ($field instanceof Field) {

                /**
                 * Keep computed fields untouched
                 */
                if ($field->computed()) {

                    return true;

                }

                return $fieldsToShow->contains($field->attribute);

            }

            return true;

        });
    }

    private function shouldApplyMegaFilter(NovaRequest $request): bool
    {

        $controller = $request->route()->controller;

        if ($controller instanceof ActionController && $request->method() === 'POST') {

            return true;

        }

        if ($request->viaRelationship() ||
            $controller instanceof ResourceShowController) {

            return false;

        }

        return $controller instanceof FilterController
            || $controller instanceof ResourceIndexController
            || $controller instanceof ResourceCountController;

    }

    private function getMegaFilterCard(NovaRequest $request): ?MegaFilter
    {
        return collect($this->cards($request))->whereInstanceOf(MegaFilter::class)->first();
    }

    private function getFilterState(NovaRequest $request, MegaFilter $card): Collection
    {
        $value = $this->getFilterColumns();

        if (! $value) {
            $value = session('mega-filter-columns');
        }

        $attributes = $card->columns()->filter(static function (Column $column) use ($value) {

            if ($column->permanent) {

                return true;

            }

            if (is_array($value) && is_bool($value = $value[ $column->attribute ])) {

                return $value;

            }

            return $column->checked;

        });

        return $attributes->pluck('attribute')->values();

    }

    public function getFilterColumns()
    {
        $filterDecoder = (new FilterDecoder(request('filters')))->decodeFromBase64String();
        $columns = collect($filterDecoder)->first(fn ($filter) => $filter[ 'class' ] === MegaFilterColumns::class);

        return optional($columns)['value'];
    }
}
