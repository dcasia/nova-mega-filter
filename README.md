# Nova Mega Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digital-creative/nova-mega-filter)](https://packagist.org/packages/digital-creative/nova-mega-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/digital-creative/nova-mega-filter)](https://packagist.org/packages/digital-creative/nova-mega-filter)
[![License](https://img.shields.io/packagist/l/digital-creative/nova-mega-filter)](https://github.com/dcasia/nova-mega-filter/blob/master/LICENSE)

Display all your filters in a card instead of a tiny dropdown!

<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/dcasia/nova-mega-filter/main/screenshots/dark.png">
  <img alt="Nova Mega Filter in Action" src="https://raw.githubusercontent.com/dcasia/nova-mega-filter/main/screenshots/light.png">
</picture>

# Installation

You can install the package via composer:

```shell
composer require digital-creative/nova-mega-filter
```

## Basic Usage

Basic demo showing the power of this package:

```php
use DigitalCreative\MegaFilter\MegaFilter;
use DigitalCreative\MegaFilter\MegaFilterTrait;

class ExampleNovaResource extends Resource {

    use MegaFilterTrait;

    public function filters(RequestRequest $request): array
    {
        return [
            MegaFilter::make([
                DateOfBirthFilter::make(),
                UserTypeFilter::make(),
            ]),
        ];
    }

}
```

And you are done!

Previously this package also had the ability to toggle columns, but since the nova 4 upgrade this functionality has been
moved away to its own package: https://github.com/dcasia/column-toggler

---

You can also add other fields alongside your Mega Filters, they will be rendered as usual:

```php
use DigitalCreative\MegaFilter\MegaFilter;
use DigitalCreative\MegaFilter\MegaFilterTrait;

class ExampleNovaResource extends Resource {

    use MegaFilterTrait;

    public function filters(RequestRequest $request): array
    {
        return [
            MegaFilter::make([ ... ]),
            
            // These will be rendered as normal on the usual tiny filter dropdown
            DateOfBirthFilter::make(),
            UserTypeFilter::make(),
        ];
    }

}
```

You can also set how many columns you want to display your filters:

```php
public function filters(RequestRequest $request): array
{
    return [
        MegaFilter::make([ ... ])->columns(3),
    ];
}
```

> Note: At the moment this package only works with a single Mega Filter per resource, adding multiple on the same resource may result in unexpected behavior.

## ⭐️ Show Your Support

Please give a ⭐️ if this project helped you!

### Other Packages You Might Like

- [Expandable Table Row](https://github.com/dcasia/expandable-table-row) - Provides an easy way to append extra data to each row of your resource tables.
- [Collapsible Resource Manager](https://github.com/dcasia/collapsible-resource-manager) - Provides an easy way to order and group your resources on the sidebar.
- [Resource Navigation Tab](https://github.com/dcasia/resource-navigation-tab) - Organize your resource fields into tabs.
- [Resource Navigation Link](https://github.com/dcasia/resource-navigation-link) - Create links to internal or external resources.
- [Nova Mega Filter](https://github.com/dcasia/nova-mega-filter) - Display all your filters in a card instead of a tiny dropdown!
- [Nova Pill Filter](https://github.com/dcasia/nova-pill-filter) - A Laravel Nova filter that renders into clickable pills.
- [Nova Slider Filter](https://github.com/dcasia/nova-slider-filter) - A Laravel Nova filter for picking range between a min/max value.
- [Nova Range Input Filter](https://github.com/dcasia/nova-range-input-filter) - A Laravel Nova range input filter.
- [Nova FilePond](https://github.com/dcasia/nova-filepond) - A Nova field for uploading File, Image and Video using Filepond.
- [Custom Relationship Field](https://github.com/dcasia/custom-relationship-field) - Emulate HasMany relationship without having a real relationship set between resources.
- [Column Toggler](https://github.com/dcasia/column-toggler) - A Laravel Nova package that allows you to hide/show columns in the index view.
- [Batch Edit Toolbar](https://github.com/dcasia/batch-edit-toolbar) - Allows you to update a single column of a resource all at once directly from the index page.

## License

The MIT License (MIT). Please see [License File](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/LICENSE) for more information.
