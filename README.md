# BusinessDays

[![Author](http://img.shields.io/badge/author-@iansltx-blue.svg?style=flat-square)](https://twitter.com/iansltx)
[![Latest Version](https://img.shields.io/github/release/iansltx/BusinessDays.svg?style=flat-square)](https://github.com/iansltx/BusinessDays/releases)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/iansltx/BusinessDays/master.svg?style=flat-square)](https://travis-ci.org/iansltx/BusinessDays)
[![HHVM Status](https://img.shields.io/hhvm/iansltx/business-days.svg?style=flat-square)](http://hhvm.h4cc.de/package/iansltx/business-days)
[![Coverage Status](https://img.shields.io/codeclimate/coverage/github/iansltx/BusinessDays.svg?style=flat-square)](https://scrutinizer-ci.com/g/iansltx/BusinessDays/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/iansltx/BusinessDays.svg?style=flat-square)](https://scrutinizer-ci.com/g/iansltx/BusinessDays)
[![Code Climate](https://img.shields.io/codeclimate/github/iansltx/BusinessDays.svg?style=flat-square)](https://codeclimate.com/github/iansltx/BusinessDays)
[![Total Downloads](https://img.shields.io/packagist/dt/iansltx/business-days.svg?style=flat-square)](https://packagist.org/packages/iansltx/business-days)

BusinessDays is a set of tools for dealing with date calculations when certain (non-business/holiday) days don't count.

This package contains two calculators, FastForwarder and Rewinder, plus a set of filters that can be used with them.

FastForwarder is configured with a day count (N) and a set of callbacks defining what *isn't* a business day, Once
configured, provide it with a start date and it'll return a date that is the first business day at least N business
days in the future. Rewinder does the same thing, except working in the opposite temporal direction.

The start date passed into the calculators can be a DateTime or DateTimeImmutable, or a subclass thereof. The value
returned will be a clone of whatever was passed in, with its timestamp updated.

This library should conform to PSRs 1, 2 and 4, and requires PHP 5.5 or newer.

## Install

Via Composer

``` bash
$ composer require iansltx/business-days
```

If you don't want Composer, you may download the source zipball directly from GitHub and load it using a PSR-4 compliant
autoloader. If you don't have such an autoloader, require `autoload.php` to get one that works for this library.

## Usage

### Filters

Calculators (FastForwarder and Rewinder) evaluate filter functions/methods/closures to see whether a given date should
be classified as a business day or not. You can provide a closure directly, or use one of the convenience methods to
compose a filter more quickly.

The convenience methods simply wrap either functions from StaticFilter or create closures via FilterFactory. Either of
these filter utility classes may be used standalone to filter DateTime objects. Additionally, these two filter classes
provide more filters than are exposed via the convenience methods on FastForwarder/Rewinder, so check them before
rebuilding a filter definition from scratch.

Note that filters requiring numeric input have internal checks that require an explicit int cast for all parameters.
Also, while skipWhen filters are executed on-add to check argument and return types, non-closure arguments are allowed,
so you can use object methods, utility functions or global functions can be used as filters in addition to closures.

Let's set up a FastForwarder, add some filters, and do some calculations...

``` php

use iansltx\BusinessDays\FilterFactory;
use iansltx\BusinessDays\StaticFilter;

// set up the instance with a day count
$ff = new iansltx\BusinessDays\FastForwarder(10);

// add a closure-based filter
$ff->skipWhen(function (\DateTimeInterface $dt) { //
    return $dt->format('m') == 12 && $dt->format('d') == 25;
}, 'christmas');

// define a closure from the filter factory, then pass it in
$dayAfterChristmasFriday = FilterFactory::monthAndDayOnDayOfWeek(12, 26, 5);
$ff->skipWhen($dayAfterChristmasFriday, 'day_after_christmas_friday');

// convenience method; saved to filter slot 'weekend'
$ff->skipWhenWeekend();

// overwrites 'weekend' slot with an identical call
$ff->skipWhen(['iansltx\BusinessDays\StaticFilter', 'isWeekend'], 'weekend');

// use some other convenience methods, this time pulling from FilterFactory and using method chaining
$ff->skipWhenNthDayOfWeekOfMonth(3, 1, 2, 'presidents_day') // third Monday of February
   ->skipWhenNthDayOfWeekOfMonth(4, 4, 11, 'thanksgiving') // fourth Thursday of November
   ->skipWhenMonthAndDay(1, 1) // auto-named to md_1_1 since a filter name wasn't provided
   ->skipWhen([StaticFilter::class, 'isGoodFriday'], 'good_friday')
   ->skipWhen([StaticFilter::class, 'isEasterMonday'], 'easter_monday');

// calculate some dates
echo $ff->exec(new \DateTime('2015-11-20 09:00:00'))->format('Y-m-d H:i:s'); // 2015-12-07 09:00:00
echo $ff->exec(new \DateTimeImmutable('2015-02-12 09:00:00'))->format('Y-m-d H:i:s'); // 2015-02-27 09:00:00

```

__NOTE:__ Fractional days are rounded up to the next whole day amount; e.g. 2.5 days will be treated as 3.

### Exporting/Importing Filter Sets

The list of filters associated with a given calculator may be dumped as an array via `getSkipWhenFilters()`. This array
can be set as filter storage for another calculation class when that class is created, so a set of filters only needs
to be defined once.

Some notes on this functionality:

1. Any filters added after construction won't propagate to the calculation object that you got the original filter
set from.
2. Passing in a set of filters at construction time will not run argument and return type tests on filters contained
therein.

With that, let's copy our filters to a new Rewinder, which as its name suggests goes in the opposite direction, and
calculate a few more dates.

``` php

// you could also create a FastForwarder with a negative day count with the same effect
$rw = new iansltx\BusinessDays\Rewinder(2, $ff->getSkipWhenFilters());

echo $rw->exec(new \DateTime('2015-02-10 09:00:00'))->format('Y-m-d H:i:s'); // 2015-02-06 09:00:00
echo $rw->exec(new \DateTime('2015-02-18 09:00:00'))->format('Y-m-d H:i:s'); // 2015-02-13 09:00:00
```

### For More Info

For more information on filter arguments etc., take a look at the source. All methods and classes have docblocks. The
callable-based syntax of skipWhen() allows for arbitrarily complex definitions of whether a date should be skipped, as
can be seen in more complex filters like isEasterMonday.

## Testing

``` bash
$ composer test
```

`humbug.json` is included if you want to do mutation testing with [Humbug](https://github.com/padraic/humbug).
Currently, not all mutations are caught; PRs are welcome to help rectify this issue. `composer test` only runs PHPUnit
tests.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details. Additional static filters/filter factories are welcome!

## Security

If you discover any security related issues, please email iansltx@gmail.com instead of using the issue tracker.

## Credits

- [Ian Littman](https://github.com/iansltx)
- [All Contributors](../../contributors)

## License

This library is BSD 2-clause licensed. Please see [License File](LICENSE.md) for more information.
