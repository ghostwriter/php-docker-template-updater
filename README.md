# Ghostwriter-PHP Docker Template Updater

[![Compliance](https://github.com/ghostwriter/ghostwriter-php-docker-template-updater/actions/workflows/compliance.yml/badge.svg)](https://github.com/ghostwriter/ghostwriter-php-docker-template-updater/actions/workflows/compliance.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/ghostwriter-php-docker-template-updater?color=8892bf)](https://www.php.net/supported-versions)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/ghostwriter-php-docker-template-updater/coverage.svg)](https://shepherd.dev/github/ghostwriter/ghostwriter-php-docker-template-updater)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/ghostwriter-php-docker-template-updater)](https://packagist.org/packages/ghostwriter/ghostwriter-php-docker-template-updater)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/ghostwriter-php-docker-template-updater?color=blue)](https://packagist.org/packages/ghostwriter/ghostwriter-php-docker-template-updater)

Helps me update various docker templates in `ghostwriter/php`.

> **Warning**
>
> This project is not finished yet, work in progress.


## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/ghostwriter-php-docker-template-updater
```

## Usage

```shell
$ ghostwriter-php-docker-template-updater composer {from-version} {to-version}

$ ghostwriter-php-docker-template-updater php {from-version} {to-version}

$ ghostwriter-php-docker-template-updater xdebug {from-version} {to-version}
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information what has changed recently.

## Security

If you discover any security related issues, please email `nathanael.esayeas@protonmail.com` instead of using the issue tracker.

## Support

- [Become a GitHub Sponsor](https://github.com/sponsors/ghostwriter)

## Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/ghostwriter-php-docker-template-updater/contributors)

## License

The BSD-3-Clause. Please see [License File](./LICENSE) for more information.
