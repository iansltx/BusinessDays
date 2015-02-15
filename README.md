# BusinessDays

[![Latest Version](https://img.shields.io/github/release/iansltx/BusinessDays.svg?style=flat-square)](https://github.com/iansltx/BusinessDays/releases)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/iansltx/BusinessDays/master.svg?style=flat-square)](https://travis-ci.org/iansltx/BusinessDays)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/iansltx/BusinessDays.svg?style=flat-square)](https://scrutinizer-ci.com/g/iansltx/BusinessDays/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/iansltx/BusinessDays.svg?style=flat-square)](https://scrutinizer-ci.com/g/iansltx/BusinessDays)
[![Total Downloads](https://img.shields.io/packagist/dt/iansltx/business-days.svg?style=flat-square)](https://packagist.org/packages/iansltx/business-days)

BusinessDays is a set of tools for dealing with date calculations when certain (non-business/holiday) days don't count.

Currently, the only tool is FastForwarder which, given a day count (N), a set of callbacks defining what *isn't* a
business day, and a start date, returns a date that is the first business day at least N business days in the future.
Both standard and immutable DateTime varieties are supported, and neither are modified when passed in as an argument.

This library should conform to PSRs 1, 2 and 4, and requires PHP 5.5 or newer, as it uses DateTimeInterface. If you
want to backport to an earlier PHP version, fork this repo and make changes on your own; I may link to that repo from
here but have no plans to relax the version requirements here.

## Install

Via Composer

``` bash
$ composer require iansltx/business-days
```

## Usage

The library setup is a bit verbose at this point, since it has no predefined filters. This will change very soon.

``` php

// set up the calculator
$ff = iansltx\BusinessDays\FastForwarder::createWithDays(10);
$ff->skipWhen(function (\DateTimeInterface $dt) {
    return in_array($dt->format('w'), [0, 6]);
}, 'weekend');
$ff->skipWhen(function (\DateTimeInterface $dt) {
    if ($dt->format('m') != 2) {
        return false;
    }

    return $dt->format('w') == 1 && $dt->format('d') > 14 && $dt->format('d') <= 21;
}, 'presidents_day');
$ff->skipWhen(function (\DateTimeInterface $dt) {
    if ($dt->format('m') != 11) {
        return false;
    }

    return $dt->format('w') == 4 && $dt->format('d') > 21 && $dt->format('d') <= 28;
}, 'thanksgiving');
$ff->skipWhen(function (\DateTimeInterface $dt) {
    return $dt->format('m') == 12 && $dt->format('d') == 25;
}, 'christmas');

// calculate some dates
echo $ff->exec(new \DateTime('2015-11-20 09:00:00'))->format('Y-m-d H:i:s'); // 2015-12-07 09:00:00
echo $ff->exec(new \DateTimeImmutable('2015-02-12 09:00:00'))->format('Y-m-d H:i:s'); // 2015-02-27 09:00:00

```

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email iansltx@gmail.com instead of using the issue tracker.

## Credits

- [Ian Littman](https://github.com/iansltx)
- [All Contributors](../../contributors)

## License

This library is BSD 2-clause licensed. Please see [License File](LICENSE.md) for more information.
