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
    public static function isWeekend(\DateTimeInterface $dt)
    {
        return in_array($dt->format('w'), [0, 6]);
    }

    /**
     * Returns true if $dt is the Monday after Easter, false otherwise
     *
     * @param \DateTimeInterface $dt
     * @return bool
     */
    public static function isEasterMonday(\DateTimeInterface $dt)
    {
        $easterMonday = (new \DateTime('@' . easter_date($dt->format('Y'))))->add(new \DateInterval('P1D'));
        return $easterMonday->format('m') === $dt->format('m') && $easterMonday->format('d') === $dt->format('d');
    }
}
