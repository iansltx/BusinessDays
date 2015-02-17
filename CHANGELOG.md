# Changelog

All Notable changes to `iansltx\BusinessDays` will be documented in this file

## 1.2.0 (2015-02-16)

- Added isGoodFriday static filter
- Fixed #3 (Easter calculation fails on HHVM) by implementing Easter calc in PHP
  (StaticFilter::getEasterDateTimeForYear, which returns a DateTimeImmutable)

## 1.1.0 (2015-02-16)

- Added standalone autoloader
- Added isEasterMonday static filter

## 1.0.0 (2015-02-15)

Initial release, with FastForwarder plus a few FilterFactory and StaticFilter methods
