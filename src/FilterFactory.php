<?php

namespace iansltx\BusinessDays;

/**
 * Class FilterFactory
 *
 * Builds filter functions based on dynamic input
 *
 * @package iansltx\BusinessDays
 */
class FilterFactory
{
    /**
     * Returns a filter that returns true if the day and month match,
     * false otherwise; the month is represented as an integer (January
     * is 1, December is 12)
     *
     * @param int $month
     * @param int $day
     * @return callable
     * @throws \OutOfBoundsException|\InvalidArgumentException
     */
    public static function monthAndDay($month, $day)
    {
        static::checkMonthAndDayArgs($month, $day);

        return function(\DateTimeInterface $dt) use ($month, $day) {
            return (int) $dt->format('m') === $month && (int) $dt->format('d') === $day;
        };
    }

    /**
     * Does type and bounds checks for monthAndDay()
     *
     * @param $month
     * @param $day
     */
    protected static function checkMonthAndDayArgs($month, $day) {
        if (!is_int($month) || !is_int($day)) {
            throw new \InvalidArgumentException('$month and $day must be integers');
        }

        if ($month < 1 || $month > 12) {
            throw new \OutOfBoundsException('$month must be 1-12');
        }

        if ($day < 1 || $day > 31) {
            throw new \OutOfBoundsException('$day must be a valid day of the month');
        }

        if ((in_array($month, [4, 6, 9, 11]) && $day > 30) || ((int) $month === 2 && $day > 29)) {
            throw new \OutOfBoundsException('Day ' . $day . ' does not exist in month ' . $month);
        }
    }

    /**
     * Returns a filter that returns true if the date is the Nth (e.g. 4rd)
     * day (e.g. Thursday, as an integer where Sunday is 0 and Saturday is 6)
     * of a given month (as an integer where January is 1 and December is 12)
     *
     * @param int $n
     * @param int $day_of_week
     * @param int $month
     * @return callable
     * @throws \OutOfBoundsException|\InvalidArgumentException
     */
    public static function nthDayOfWeekOfMonth($n, $day_of_week, $month) {
        static::checkNthDayArgs($n, $day_of_week, $month);

        $lowerBound = ($n - 1) * 7;
        $upperBound = $n * 7;

        return function(\DateTimeInterface $dt) use ($month, $day_of_week, $lowerBound, $upperBound) {
            if ((int) $dt->format('m') !== $month) {
                return false;
            }

            return ((int) $dt->format('w')) === $day_of_week &&
                    $dt->format('d') > $lowerBound && $dt->format('d') <= $upperBound;
        };
    }

    /**
     * Does type and bounds checks for nThDayOfWeekOfMonth()
     *
     * @param $n
     * @param $day_of_week
     * @param $month
     */
    protected static function checkNthDayArgs($n, $day_of_week, $month) {
        if (!is_int($n) || !is_int($day_of_week) || !is_int($month)) {
            throw new \InvalidArgumentException('$n, $day_of_week and $month must be integers');
        }

        if ($n < 1 || $n > 5) {
            throw new \OutOfBoundsException('$n must be 1-5');
        }

        if ($day_of_week < 0 || $day_of_week > 6) {
            throw new \OutOfBoundsException('$day_of_week must be 0-6');
        }

        if ($month < 1 || $month > 12) {
            throw new \OutOfBoundsException('$month must be 1-12');
        }
    }
}
