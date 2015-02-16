# BusinessDays

[![Author](http://img.shields.io/badge/author-@iansltx-blue.svg?style=flat-square)](https://twitter.com/iansltx)
[![Latest Version](https://img.shields.io/github/release/iansltx/BusinessDays.svg?style=flat-square)](https://github.com/iansltx/BusinessDays/releases)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/iansltx/BusinessDays/master.svg?style=flat-square)](https://travis-ci.org/iansltx/BusinessDays)
[![HHVM Status](https://img.shields.io/hhvm/iansltx/business-days.svg?style=flat-square)](http://hhvm.h4cc.de/package/iansltx/business-days)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/iansltx/BusinessDays.svg?style=flat-square)](https://scrutinizer-ci.com/g/iansltx/BusinessDays/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/iansltx/BusinessDays.svg?style=flat-square)](https://scrutinizer-ci.com/g/iansltx/BusinessDays)
[![Code Climate](https://codeclimate.com/github/iansltx/BusinessDays/badges/gpa.svg)](https://codeclimate.com/github/iansltx/BusinessDays)
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

If you don't want Composer, you may download the source zipball directly from GitHub and load it using a PSR-4 compliant
autoloader. If you don't have such an autoloader, require `autoload.php` to get one that works for this library.

## Usage

FastForwarder evaluates filter functions/methods/closures to see whether a given date should be classified as a business
day or not. You can provide a closure directly, or use one of the convenience methods to compose a filter more quickly.

The convenience methods simply wrap either functions from StaticFilter or create closures via FilterFactory. Either of
these filter utility classes may be used standalone to filter DateTime objects.

Note that filters requiring numeric input have internal checks that require an explicit int cast for all parameters.
Also, while skipWhen filters are executed on-add to check argument and return types, non-closure arguments are allowed,
so you can use object methods, utility functions or global functions can be used as filters in addition to closures.

Let's see FastForwarder in action...

``` php

// set up the instance with a day count
$ff = iansltx\BusinessDays\FastForwarder::createWithDays(10);

// add a closure-based filter
$ff->skipWhen(function (\DateTimeInterface $dt) { //
    return $dt->format('m') == 12 && $dt->format('d') == 25;
}, 'christmas');

// convenience method; saved to filter slot 'weekend'
$ff->skipWhenWeekend();

// overwrites 'weekend' slot with an identical call
$ff->skipWhen(['iansltx\BusinessDays\StaticFilter', 'isWeekend'], 'weekend');

// use some other convenience methods, this time pulling from FilterFactory and using method chaining
$ff->skipWhenNthDayOfWeekOfMonth(3, 1, 2, 'presidents_day') // third Monday of February
   ->skipWhenNthDayOfWeekOfMonth(4, 4, 11, 'thanksgiving') // fourth Thursday of November
   ->skipWhenMonthAndDay(1, 1); // auto-named to md_1_1 since a filter name wasn't provided

// calculate some dates
echo $ff->exec(new \DateTime('2015-11-20 09:00:00'))->format('Y-m-d H:i:s'); // 2015-12-07 09:00:00
echo $ff->exec(new \DateTimeImmutable('2015-02-12 09:00:00'))->format('Y-m-d H:i:s'); // 2015-02-27 09:00:00

```

For more information on filter arguments etc., take a look at the source. All methods and classes have docblocks.

## Testing

``` bash
$ phpunit
```

`humbug.json` is included if you want to do mutation testing with [Humbug](https://github.com/padraic/humbug).
Currently, not all mutations are caught; PRs are welcome to help rectify this issue.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details. Additional static filters/filter factories are welcome!

## Security

If you discover any security related issues, please email iansltx@gmail.com instead of using the issue tracker.

## Credits

- [Ian Littman](https://github.com/iansltx)
- [All Contributors](../../contributors)

## License

This library is BSD 2-clause licensed. Please see [License File](LICENSE.md) for more information.
