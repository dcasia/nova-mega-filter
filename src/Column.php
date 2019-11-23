<?php

namespace DigitalCreative\MegaFilter;

use Illuminate\Support\Str;
use JsonSerializable;
use Laravel\Nova\Filters\Filter;

class Column implements JsonSerializable
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $attribute;

    /**
     * @var array
     */
    public $filters;

    /**
     * @var bool
     */
    public $permanent = false;

    /**
     * @var bool
     */
    public $checked = false;

    /**
     * Column constructor.
     *
     * @param string $label
     * @param string $attribute
     * @param array $filters
     */
    public function __construct(string $label, string $attribute = null, array $filters = [])
    {
        $this->label = $label;
        $this->attribute = $attribute ?? str_replace(' ', '_', Str::lower($label));
        $this->filters = $filters;
    }

    /**
     * Create a new element.
     *
     * @param array $arguments
     *
     * @return static
     */
    public static function make(...$arguments): self
    {
        return new static(...$arguments);
    }

    public function permanent(bool $permanent = true): self
    {
        $this->permanent = $permanent;

        return $this;
    }

    public function checked(bool $checked = true): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function addFilter(Filter $filter): self
    {
        $this->filters[] = $filter;

        return $this;
    }

    public function filters(array $filters): self
    {
        $this->filters = $filters;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'label' => $this->label,
            'attribute' => $this->attribute,
            'permanent' => $this->permanent,
            'checked' => $this->checked,
            'filters' => $this->filters
        ];
    }
}
