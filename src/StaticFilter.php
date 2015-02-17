<?php

namespace iansltx\BusinessDays;

/**
 * Class StaticFilter
 *
 * A library of static filter functions that can be used directly
 *
 * @package iansltx\BusinessDays
 */
class StaticFilter
{
    protected static $easterByYear = []; // cache of Easter DateTimeImmutable objects, indexed by year

    /**
     * Returns true if $dt is on a Saturday or Sunday, false otherwise
     *
     * @param \DateTimeInterface $dt
     * @return bool
     */
    public static function isWeekend(\DateTimeInterface $dt)
    {
        return in_array($dt->format('w'), [0, 6]);
    }

    /**
     * Returns true if $dt is the Monday after Western Easter, false otherwise
     *
     * @param \DateTimeInterface $dt
     * @return bool
     */
    public static function isEasterMonday(\DateTimeInterface $dt)
    {
        $easterMonday = static::getEasterDateTimeForYear($dt->format('Y'))->add(new \DateInterval('P1D'));
        return $easterMonday->format('m') === $dt->format('m') && $easterMonday->format('d') === $dt->format('d');
    }

    /**
     * Returns true if $dt is the Friday before Western Easter, false otherwise
     *
     * @param \DateTimeInterface $dt
     * @return bool
     */
    public static function isGoodFriday(\DateTimeInterface $dt)
    {
        $goodFriday = static::getEasterDateTimeForYear($dt->format('Y'))->sub(new \DateInterval('P2D'));
        return $goodFriday->format('m') === $dt->format('m') && $goodFriday->format('d') === $dt->format('d');
    }

    /**
     * Gets an immutable DateTime object set to midnight UTC of Western Easter
     * for a given year. Built because HHVM doesn't include easter_date() or
     * easter_day(). Credit to https://www.drupal.org/node/1180480 for the
     * original implementation.
     *
     * To get a runtime-agnostic result comparable to PHP4+'s easter_date(),
     * call getTimestamp() on the returned value of this function.
     *
     * @param int $year
     * @return \DateTimeImmutable
     */
    public static function getEasterDateTimeForYear($year) {
        if (!isset(static::$easterByYear[$year])) { // build cached value if it doesn't exist
            $century = intval($year / 100);
            $centuryOverFour = intval($century / 4);
            $centuryMod4 = $century % 4;

            $twoDigitYear = $year % 100;
            $twoDigitYearOver4 = intval($twoDigitYear / 4);
            $twoDigitYearMod4 = $twoDigitYear % 4;

            $yearMod19 = $year % 19;

            $interF = intval(($century + 8) / 25);
            $interG = intval(($century - $interF + 1) / 3);
            $interH = (19 * $yearMod19 + $century - $centuryOverFour - $interG + 15) % 30;
            $interL = (32 + 2 * $centuryMod4 + 2 * $twoDigitYearOver4 - $interH - $twoDigitYearMod4) % 7;
            $interM = intval(($yearMod19 + 11 * $interH + 22 * $interL) / 451);
            $interP = ($interH + $interL - 7 * $interM + 114) % 31;

            $monthNumber = intval(($interH + $interL - 7 * $interM + 114) / 31);
            $day = $interP + 1;

            static::$easterByYear[$year] = \DateTimeImmutable::createFromFormat(
                   'Y-m-d', $year . '-' . $monthNumber . '-' . $day, new \DateTimeZone('UTC'));
        }

        return static::$easterByYear[$year]; // return cached value
    }
}
