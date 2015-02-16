<?php

namespace iansltx\BusinessDays;

use \DateTime;
use \DateTimeImmutable;
use \DateTimeInterface;
use \InvalidArgumentException;
use \DateInterval;

/**
 * Class FastForwarder
 *
 * Provides an easy way to calculate dates that are a specified number of
 * "business bays" in the future relative to a supplied date. This is achieved
 * by first adding a series of filter callbacks that define what is NOT a
 * business day.
 *
 * @package iansltx\BusinessDays
 */
class FastForwarder
{
    protected $skipWhen = [];
    protected $numDays = 0;

    protected function __construct()
    {
    }

    /**
     * Creates a FastForwarder with a defined number of business days
     *
     * @param int $num_days the number of business days from the supplied date to
     *  the end date
     * @return static
     */
    public static function createWithDays($num_days)
    {
        $fastForwarder = new static;
        $fastForwarder->numDays = $num_days;
        return $fastForwarder;
    }

    /**
     * @param callable $filter takes a DateTimeInterface, returns true if that date
     *  is not a business day (and should be skipped) and false if it is
     * @param string $filter_name a way to refer to the filter; for future use
     * @return $this
     */
    public function skipWhen($filter, $filter_name)
    {
        if (!is_bool(call_user_func($filter, new DateTimeImmutable()))) {
            throw new InvalidArgumentException('$filter must accept a \DateTimeInterface and return a boolean.');
        }

        $this->skipWhen[$filter_name] = $filter;
        return $this;
    }

    /**
     * Convenience method for adding a filter that marks Saturday and Sunday as
     * non-business days.
     *
     * @return $this
     */
    public function skipWhenWeekend()
    {
        $this->skipWhen['weekend'] = [StaticFilter::class, 'isWeekend'];
        return $this;
    }

    /**
     * Convenience method for adding a filter that marks a certain day/month as
     * a non-business day.
     *
     * @param int $month
     * @param int $day
     * @param null|string $name optional filter name rather than default of md_$month_$day
     * @return $this
     */
    public function skipWhenMonthAndDay($month, $day, $name = null)
    {
        $this->skipWhen[$name ?: 'md_' . $month . '_' . $day] =
                FilterFactory::monthAndDay($month, $day);
        return $this;
    }

    /**
     * Convenience method for adding a filter that marks the Nth (e.g. 4th) weekday
     * (e.g. Thursday, as an integer e.g. 4) of a given month (as an integer) as
     * a non-business day.
     *
     * @param int $n
     * @param int $day_of_week
     * @param int $month
     * @param null|string $name optional filter name rather than default of ndm_$n_$day_of_week_$month
     * @return $this
     */
    public function skipWhenNthDayOfWeekOfMonth($n, $day_of_week, $month, $name = null)
    {
        $this->skipWhen[$name ?: 'ndm_' . $n . '_' . $day_of_week . '_' . $month] =
                FilterFactory::nthDayOfWeekOfMonth($n, $day_of_week, $month);
        return $this;
    }

    /**
     * Calculates a date X days after $start_date, where X was supplied in
     * static::createWithDays(); if the end date would land on a non-business
     * day, the first business day after that date is returned.
     *
     * @param DateTimeInterface $start_date
     * @return DateTimeInterface same type as $start_date
     */
    public function exec(DateTimeInterface $start_date)
    {
        $startTz = $start_date->getTimezone();
        $newDt = (new DateTime($start_date->format('c')))->setTimeZone($startTz);

        $daysLeft = $this->numDays;
        $dateInterval = new DateInterval('P1D');

        while ($daysLeft > 0) {
            if ($this->getSkippedBy($newDt) === false) {
                --$daysLeft;
            }

            $newDt->add($dateInterval);
        }

        do { // if end date falls on a skipped day, keep skipping until a valid day is found
            $skipFilterName = $this->getSkippedBy($newDt);
            if ($skipFilterName !== false) {
                $newDt->add($dateInterval);
            }
        } while ($skipFilterName !== false);

        return $start_date instanceof DateTime ? $newDt->setTimezone($startTz) :
                new DateTimeImmutable($newDt->format('Y-m-d H:i:s'), $startTz);
    }

    /**
     * Returns the name of the first "skip when" filter that matches the
     * supplied date, or false if none match (i.e. $dt is a business day).
     *
     * @param DateTime $dt
     * @return false|string
     */
    protected function getSkippedBy(DateTime $dt)
    {
        foreach ($this->skipWhen as $name => $fn) {
            if (call_user_func($fn, $dt)) {
                return $name;
            }
        }

        return false;
    }
}
