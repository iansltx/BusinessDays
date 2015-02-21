<?php

namespace iansltx\BusinessDays;

use DateTimeInterface;
use DateTime;
use DateInterval;
use DateTimeImmutable;

/**
 * Class FastForwarder
 *
 * Provides an easy way to calculate dates that are a specified number of
 * "business bays" in the future relative to a supplied date. This is achieved
 * by first adding a series of filter callbacks that define what is NOT a
 * business day.
 *
 * Calculates a date X days after $start_date, where X was supplied in
 * static::createWithDays(); if the end date would land on a non-business
 * day, the first business day after that date is returned.
 *
 * A negative day count may be entered into the constructor to count in the
 * opposite direction (days in the past vs. in the future). Or just use
 * Rewinder.
 *
 * @package iansltx\BusinessDays
 */
class FastForwarder
{
    use SkipWhenTrait;

    protected $numDays = 0;

    /** @var DateInterval */
    protected $interval;

    /**
     * Static factory method for backward compatibility
     *
     * @param $num_days
     * @param array $skip_when
     * @return static
     */
    public static function createWithDays($num_days, array $skip_when = [])
    {
        return new static($num_days, $skip_when);
    }

    /**
     * Creates an instance with a defined number of business days
     *
     * @param int $num_days the number of business days from the supplied date to
     *  the calculated date
     * @param array $skip_when pre-defined filters to add to skipWhen without testing
     */
    public function __construct($num_days, array $skip_when = [])
    {
        $this->numDays = abs($num_days);
        $this->interval = new DateInterval('P1D');
        $this->interval->invert = ($this->numDays != $num_days); // invert iterator direction if negative day count
        $this->replaceSkipWhen($skip_when);
    }

    /**
     * Iterates through dates in steps of $this->interval until $this->numDays
     * is zero. Then, if the current day is not a business day, iterate until
     * the current day is a business day. Return the result in the same format
     * as was supplied.
     *
     * @param DateTimeInterface $start_date
     * @return DateTimeInterface clone of $start_date with a modified timestamp
     */
    public function exec(DateTimeInterface $start_date)
    {
        $startTz = $start_date->getTimezone();
        $newDt = (new DateTime($start_date->format('c')))->setTimeZone($startTz);

        $daysLeft = $this->numDays;

        while ($daysLeft > 0) {
            if ($this->getSkippedBy($newDt) === false) {
                --$daysLeft;
            }

            $newDt->add($this->interval);
        }

        do { // if end date falls on a skipped day, keep skipping until a valid day is found
            $skipFilterName = $this->getSkippedBy($newDt);
            if ($skipFilterName !== false) {
                $newDt->add($this->interval);
            }
        } while ($skipFilterName !== false);

        /** @var DateTime|DateTimeImmutable $clonedDt */
        $clonedDt = clone $start_date;
        return $clonedDt->setTimestamp($newDt->getTimestamp());
    }
}
