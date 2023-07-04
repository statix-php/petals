# A PHP templating engine based on Laravel Blade

[![Latest Version on Packagist](https://img.shields.io/packagist/v/statix/petals.svg?style=flat-square)](https://packagist.org/packages/statix/petals)
[![Tests](https://github.com/statix-php/petals/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/statix-php/petals/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/statix/petals.svg?style=flat-square)](https://packagist.org/packages/statix/petals)

![Banner image](.github/banner.jpg)

Petals is a zero dependency PHP templating engine based on Laravel Blade. This was built as a fun experiment to play with ChatGPT and GitHub Co-pilot, between the two AI tools they wrote ~60% of the code.

## Installation

You can install the package via composer:

```bash
composer require statix-php/petals
```

## Usage

```php
use Statix\Petals\TemplatingEngine;

// require the composer autoloader
require __DIR__.'/vendor/autoload.php';

$engine = new TemplatingEngine(
    templates: __DIR__.'/templates',
    cachePath: __DIR__.'/cache',
);

// Render the templates/app.blade.php template
$engine->render('app', [
    'message' => 'Hello world!',
]);

// Render the given string
$engine->renderString('Hello {{ $name }}! The unix timestamp is {{ $time }}', [
    'name' => 'world',
    'time' => time(),
]);

// Compile the template to the cache directory, the compiled template will be used to render the template when render is called
$engine->complile('app');

// Compile the given string to the cache directory, if render is called on the same string, it will be rendered from the compiled cache template
$engine->compileString('Hello {{ $name }}! The unix timestamp is {{ $time }}');

// Clear the compiled templates
$engine->clearCache();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Security Vulnerabilities

// todo 

## Credits

- [Wyatt Castaneda](https://github.com/statix-php)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
