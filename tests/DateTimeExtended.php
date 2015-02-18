<?php

namespace iansltx\BusinessDays\Test;

use DateTimeZone;

class DateTimeExtended extends \DateTime
{
    protected $somethingElse;

    public function __construct($time = 'now', DateTimeZone $timezone = null)
    {
        parent::__construct($time, $timezone);
        $this->somethingElse = rand();
    }

    public function getSomethingElse() {
        return $this->somethingElse;
    }
}