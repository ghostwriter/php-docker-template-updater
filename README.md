# Ghostwriter-PHP Docker Template Updater

[![Compliance](https://github.com/ghostwriter/ghostwriter-php-docker-template-updater/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/ghostwriter-php-docker-template-updater/actions/workflows/compliance.yml)

Helps me update various docker templates in `ghostwriter/php`.

> **Warning**
>
> This project is not meant to be used by others but you're welcome too.

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/ghostwriter-php-docker-template-updater
```

## Usage

``` bash
ghostwriter-php-docker-template-updater composer {from:version} {to:version}

ghostwriter-php-docker-template-updater ext {from:extension-version} {to:extension-version}

ghostwriter-php-docker-template-updater php {from:version} {to:version}

ghostwriter-php-docker-template-updater xdebug {from:version} {to:version}
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email `nathanael.esayeas@protonmail.com` instead of using the issue tracker.

## Sponsors

- [Become a GitHub Sponsor](https://github.com/sponsors/ghostwriter)

## Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/ghostwriter-php-docker-template-updater/contributors)

## License

The BSD-3-Clause. Please see [License File](./LICENSE) for more information.
