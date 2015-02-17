<?php

namespace iansltx\BusinessDays;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateInterval;

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
    use SkipWhenTrait;

    protected function __construct()
    {
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
}
