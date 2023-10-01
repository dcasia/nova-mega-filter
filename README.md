# Nova Mega Filter

[![Latest Version on Packagist](https://img.shields.io/packagist/v/digital-creative/nova-mega-filter)](https://packagist.org/packages/digital-creative/nova-mega-filter)
[![Total Downloads](https://img.shields.io/packagist/dt/digital-creative/nova-mega-filter)](https://packagist.org/packages/digital-creative/nova-mega-filter)
[![License](https://img.shields.io/packagist/l/digital-creative/nova-mega-filter)](https://github.com/dcasia/nova-mega-filter/blob/master/LICENSE)

Display all your filters in a card instead of a tiny dropdown!.

<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/dcasia/nova-mega-filter/nova4/screenshots/dark.png">
  <img alt="Nova Mega Filter in Action" src="https://raw.githubusercontent.com/dcasia/nova-mega-filter/nova4/screenshots/light.png">
</picture>

# Installation

You can install the package via composer:

```
composer require digital-creative/nova-mega-filter
```

## Basic Usage

Basic demo showing the power of this package:

```php

use DigitalCreative\MegaFilter\MegaFilterCard;
use DigitalCreative\MegaFilter\MegaFilterTrait;
use DigitalCreative\MegaFilter\Column;

class ExampleNovaResource extends Resource {

    use MegaFilterTrait;

    public function filters(RequestRequest $request): array
    {
        return [
            MegaFilterCard::make([
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

## License

The MIT License (MIT). Please see [License File](https://raw.githubusercontent.com/dcasia/nova-mega-filter/master/LICENSE) for more information.
