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
    /**
     * Returns true if $dt is on a Saturday or Sunday, false otherwise
     *
     * @param \DateTimeInterface $dt
     * @return bool
     */
    public static function isWeekend(\DateTimeInterface $dt) {
        return in_array($dt->format('w'), [0, 6]);
    }
}
