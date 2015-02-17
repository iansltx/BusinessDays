<?php

namespace iansltx\BusinessDays;

use DateTimeInterface;
use DateInterval;

/**
 * Class Rewinder
 *
 * Provides an easy way to calculate dates that are a specified number of
 * "business bays" in the past relative to a supplied date. This is achieved
 * by first adding a series of filter callbacks that define what is NOT a
 * business day.
 *
 * @package iansltx\BusinessDays
 */
class Rewinder extends PeriodIterator
{
    /**
     * Calculates a date X days before $start_date, where X was supplied in
     * static::createWithDays(); if the end date would land on a non-business
     * day, the last business day before that date is returned.
     *
     * @param DateTimeInterface $start_date
     * @return DateTimeInterface same type as $start_date
     */
    public function exec(DateTimeInterface $start_date)
    {
        return $this->iterate($start_date, new DateInterval('P1D'), true);
    }
}
