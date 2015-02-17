<?php

namespace iansltx\BusinessDays;

use DateTimeInterface;
use DateTime;
use DateTimeImmutable;
use DateInterval;

abstract class PeriodIterator
{
    use SkipWhenTrait;

    protected function __construct()
    {
    }

    /**
     * @param DateTimeInterface $start_date
     * @param DateInterval $step
     * @param bool $is_reversed
     * @return DateTime|DateTimeImmutable
     */
    protected function iterate(DateTimeInterface $start_date, DateInterval $step, $is_reversed = false)
    {
        $startTz = $start_date->getTimezone();
        $newDt = (new DateTime($start_date->format('c')))->setTimeZone($startTz);

        $daysLeft = $this->numDays;

        while ($daysLeft > 0) {
            if ($this->getSkippedBy($newDt) === false) {
                --$daysLeft;
            }

            !$is_reversed ? $newDt->add($step) : $newDt->sub($step);
        }

        do { // if end date falls on a skipped day, keep skipping until a valid day is found
            $skipFilterName = $this->getSkippedBy($newDt);
            if ($skipFilterName !== false) {
                !$is_reversed ? $newDt->add($step) : $newDt->sub($step);
            }
        } while ($skipFilterName !== false);

        return $start_date instanceof DateTime ? $newDt->setTimezone($startTz) :
            new DateTimeImmutable($newDt->format('Y-m-d H:i:s'), $startTz);
    }

    /**
     * @param DateTimeInterface $start_date
     * @return DateTimeInterface
     */
    abstract public function exec(DateTimeInterface $start_date);
}
