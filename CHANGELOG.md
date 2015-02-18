# Changelog

All Notable changes to `iansltx\BusinessDays` will be documented in this file

## NEXT

- Added bulk filter import/export to FastForwarder
- Added Rewinder for iterating backward from, rather than forward from, a given date
- Moved non-algorithmic functionality into SkipWhenTrait for use by other potential classes
- Fixed exec() return type when argument subclasses \DateTime or \DateTimeImmutable

## 1.2.0 (2015-02-16)

- Added isGoodFriday static filter
- Fixed #3 (Easter calculation fails on HHVM) by implementing Easter calc in PHP
  (StaticFilter::getEasterDateTimeForYear, which returns a DateTimeImmutable)

## 1.1.0 (2015-02-16)

- Added standalone autoloader
- Added isEasterMonday static filter

## 1.0.0 (2015-02-15)

Initial release, with FastForwarder plus a few FilterFactory and StaticFilter methods
