# Artisan commands to rapidly develop your apps.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/conquest/assemble.svg?style=flat-square)](https://packagist.org/packages/conquest/assemble)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/conquest/assemble/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/conquest/assemble/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/conquest/assemble/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/conquest/assemble/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/conquest/assemble.svg?style=flat-square)](https://packagist.org/packages/conquest/assemble)

Assemble is a package to eliminate the boilerplate code you often write when creating new files in your Laravel application. It is built for the Conquest ecosystem of packages, and so is a Javascript based implementation. 

Users will need to publish the applications `stubs` to override them, as they are built by default for the private `Conquest Legion` boilerplate kit.

## Installation

You can install the package via composer:

```bash
composer require conquest/assemble
```

Customise the paths and extensions through by publishing config file with:

```bash
php artisan vendor:publish --tag="assemble-config"
```

This is the contents of the published config file:

```php
return [
    'extension' => 'vue',
    'paths' => [
        'page' => 'js/Pages',
        'modal' => 'js/Modals',
        'component' => 'js/Components',
    ],
    'base_route' => 'dashboard',
];

```

You should also publish the stubs to customise them:
    
```bash
php artisan vendor:publish --tag="assemble-stubs"
```


## Usage
Use the provided commands to generate the boilerplate files via CLI. The available commands are:

```bash
php artisan make:page
php artisan make:modal
php artisan make:conquest
php artisan make:js-component
php artisan create:user {email} {password}
```

### User Creation
It is **strongly** recommended you extend the `UserCreateCommand` to fit your user creation flow. This command is only designed for the Laravel starter kits, using the `UserFactory` that is provided.

Some methods are provided, but not implemented for this command. By extending, you can opt into them without needing to write the option methods yourself.

### Conquest
Conquest is a compound command, using highly opionated conventions to generate out controllers, requests and Javascript pages (by default), with the ability of generating complete file structures dependent on the options provided to it. All arguments to the command must be camel-cased and in the form `ModelMethod`. The method must be the final part of the argument.

By default, passing `ModelMethod` will create a `Request` and single-action `Controller`, with a `Page` or `Modal` being generated if the method segment of the name matches the required case.

If you do include options, your name argument must contain one of the 8 keywords, or no keyword at all. Having other names can result in undesirable naming conventions to be generated.

The naming convention uses 8 keywords to generate out different files.
- `Index`: Generates a page
- `Show`: Generates a page by default, --modal flag will generate a modal
- `Create`: Generates a form page by default, --modal flag will generate a form modal
- `Store`: No page
- `Edit`: Generates a form page by default, --modal flag will generate a form modal
- `Update`: No page
- `Delete`: Generates a modal by default, --page flag will generate a page
- `Destroy`: No page

It will additionally remove pluralisation from the model name, and use the singular form for the file names.

The complete list of options to provide is:
- `--page`: Force a page to be generated
- `--modal`: Force a modal to be generated
- `--model`: Generates a model, and cretaes the desired endpoint
- `--seeder`: Generates a seeder for the given `--model`, if not supplied, nothing will happen
- `--factory`: Generates a factory for the given `--model`, if not supplied, nothing will happen
- `--migration`: Generates a controller for the given `--model`, if not supplied, nothing will happen
- `--policy`: Generates a policy for the given `Model`
- `--resource`: Generates a resource for the given `Model`
- `--crud`: Will create all 8 actions for a given `Model` name
- `--force`: Overwrites existing files. Applies to all files to be generated
- `--route`: Add the endpoint(s) to the end of your `web.php`, or the `route/` file specified as the argument. If `crud` is specified, this will also group the arguments under a header
- `--all`: Executes all available options

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Joshua Wallace](https://github.com/jdw5)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
