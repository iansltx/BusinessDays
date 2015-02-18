<?php

namespace iansltx\BusinessDays;

/**
 * Class Rewinder
 *
 * Provides an easy way to calculate dates that are a specified number of
 * "business bays" in the past relative to a supplied date. This is achieved
 * by first adding a series of filter callbacks that define what is NOT a
 * business day.
 *
 * Calculates a date X days before $start_date, where X was supplied in
 * static::createWithDays(); if the end date would land on a non-business
 * day, the last business day before that date is returned.
 *
 * @package iansltx\BusinessDays
 */
class Rewinder extends FastForwarder
{
    protected function __construct()
    {
        parent::__construct();
        $this->interval->invert = 1;
    }
}
