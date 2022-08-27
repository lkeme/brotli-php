[![GitHub Workflow Status](https://img.shields.io/github/workflow/status/lkeme/brotli-php/Tests?style=flat-square)](https://github.com/nlemoine/brotli-php/actions/workflows/tests.yml?query=branch%3Amaster+workflow%3ATests)

This library adds Brotli support to PHP (^7.4 || ^8.0). Batteries included.

```php
function brotli_compress(string $data, int $quality = 11): string

function brotli_uncompress(string $data): string
```

It is a fork of [vdechenaux/brotli-php](https://github.com/vdechenaux/brotli-php). Main differences:

- avoid usage of `ob_start` in [`\Symfony\Component\Process\Process`](https://github.com/symfony/process/blob/b8d6eff26e48187fed15970799f4b605fa7242e4/Process.php#L1383-L1386) so you can use it inside an `ob_start` callback.
- comes with prebuilt binaries and automatic system guessing

## Installation

```
$ composer require hellonico/brotli
```

## Binaries

### `brotli` is not available on your system/server

Prebuilt binaries included for the following systems:

- Linux (x86_64/i386/aarch64/armv7)
- Mac OS
- Windows

### `brotli` is available on your system/server

If `brotli` is available on your server, you set its path using:

```
\HelloNico\Brotli\Brotli::setBinaryPath('brotli');
```

or

```
\HelloNico\Brotli\Brotli::setBinaryPath('/some/dir/brotli');
```

## Tests

```
composer test
```
