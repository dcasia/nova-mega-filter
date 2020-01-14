# Nova Mega Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digital-creative/nova-mega-filter)](https://packagist.org/packages/digital-creative/nova-mega-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/digital-creative/nova-mega-filter)](https://packagist.org/packages/digital-creative/nova-mega-filter)
[![License](https://img.shields.io/packagist/l/digital-creative/nova-mega-filter)](https://github.com/dcasia/nova-mega-filter/blob/master/LICENSE)

Control how the columns, filters, and actions are shown on nova resources. Perfect for large resources & many filters.

## Installation

Install the package via composer:

```
composer require digital-creative/nova-mega-filter
```

## Quick start

Here's a very condensed guide to get you started while showing the power & possibilities of this field:

```php

use DigitalCreative\MegaFilter\MegaFilter;
use DigitalCreative\MegaFilter\HasMegaFilterTrait;
use DigitalCreative\MegaFilter\Column;

class ExampleNovaResource extends Resource {

    use HasMegaFilterTrait; // Important!!

    public function fields(Request $request)
    {
        return [
            Text::make('Name'),
            Boolean::make('Status'),
            Date::make('Created at')
        ];
    }

    public function cards(Request $request)
    {
        return [
            MegaFilter::make([
                'filters' => [
                    new StatusFilter(),
                    new CreatedAtFilter()
                ],
                'columns' => [
                    Column::make('Name'),
                    Column::make('Status'),
                    Column::make('Created at')->addFilter(new CreatedAtFilter())
                ]
            ])
        ];
    }
}
```

### Columns

Columns reflects every column that is normally shown on your table resource, however you are free to give your user the 
ability to toggle it on/off:

![Columns in Action](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/screenshots/columns-demo.png)

```php
use DigitalCreative\MegaFilter\Column;
use DigitalCreative\MegaFilter\HasMegaFilterTrait;
use DigitalCreative\MegaFilter\MegaFilter;

MegaFilter::make([
    'columns' => [
        Column::make($label),
        Column::make($label, $attribute),
        Column::make($label, $attribute, $filters),
        new Column($label, $attribute, $filters),
    ],
])
```

You can add additional filters that only appears when the desired column is enabled:

```php
MegaFilter::make([
    'columns' => [
        Column::make('Gender')->addFilter(new GenderFilter())
    ],
])
```

![Columns in Action](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/screenshots/columns-gender-filter-demo.png)

> If you want to have filters that are always shown, use the 'filters' option bellow

You can also define columns that can not be toggled by the user and will be always present on the table resource:

```php
MegaFilter::make([
    'columns' => [
        Column::make('Name')->permanent()
    ],
])
```

When using `->permanent()` every filter that the column may define will be also present

Other column methods include the ability to have a column default checked

```php
MegaFilter::make([
    'columns' => [
        Column::make('Name')->checked() // Checked by default
    ],
])
```

### Filters

The `filters` key accepts an array of any instance of the default Nova filter class or third party. 

```php
MegaFilter::make([
    'filters' => [
       new BirthdayFilter(),
       new UserTypeFilter(),
       new GenderFilter(),
    ],
])
```

### Actions

Actions will use the same Nova action mechanism

```php
MegaFilter::make([
    'actions' => [
       new ExportClientAsExcell(),
    ],
])
```

![Columns in Action](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/screenshots/action-demo.png)

If you have multiple actions defined, a dropdown will be shown:

![Columns in Action](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/screenshots/action-demo-2.png)

The columns selected will also be given to your action, you can access them directly from the request:

```php
public function handle(ActionFields $fields, Collection $models)
{

    $columns = json_decode(request()->input('columns'));

    dd($columns);

}
```

### Settings

The settings key is optional as it comes with some good defaults, but feel free to override it to suit your needs.

```php
MegaFilter::make([
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

    ],
])
```

## License

The MIT License (MIT). Please see [License File](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/LICENSE) for more information.
