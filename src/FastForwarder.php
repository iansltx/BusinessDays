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
 * @package iansltx\BusinessDays
 */
class FastForwarder
{
    use SkipWhenTrait;

    /** @var DateInterval */
    protected $interval;

    protected function __construct()
    {
        $this->interval = new DateInterval('P1D');
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
