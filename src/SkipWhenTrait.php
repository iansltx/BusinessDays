<?php

namespace iansltx\BusinessDays;

use DateTimeImmutable;
use InvalidArgumentException;
use DateTime;

trait SkipWhenTrait
{
    protected $skipWhen = [];
    protected $numDays = 0;

    /**
     * Creates an instance with a defined number of business days
     *
     * @param int $num_days the number of business days from the supplied date to
     *  the calculated date
     * @param array $skip_when pre-defined filters to add to skipWhen without testing
     * @return static
     */
    public static function createWithDays($num_days, array $skip_when = [])
    {
        $class = new static;
        $class->numDays = $num_days;
        $class->skipWhen = $skip_when;

        return $class;
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
     * Returns an associative array of filters added via skipWhen()
     *
     * @return array
     */
    public function getSkipWhenFilters() {
        return $this->skipWhen;
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
