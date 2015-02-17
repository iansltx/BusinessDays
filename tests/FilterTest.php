<?php

namespace iansltx\BusinessDays\Test;

use iansltx\BusinessDays\StaticFilter;
use iansltx\BusinessDays\FilterFactory;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testWeekendTrue()
    {
        $this->assertTrue(StaticFilter::isWeekend(new \DateTime('next Sunday')));
    }

    public function testWeekendFalse()
    {
        $this->assertFalse(StaticFilter::isWeekend(new \DateTime('last Tuesday')));
    }

    public function testMonthDayNonInteger()
    {
        $this->setExpectedException('\InvalidArgumentException');
        FilterFactory::monthAndDay(1, '1');
    }

    public function testMonthDayInvalidMonth()
    {
        $this->setExpectedException('\OutOfBoundsException');
        FilterFactory::monthAndDay(13, 1);
    }

    public function testMonthDayInvalidDay()
    {
        $this->setExpectedException('\OutOfBoundsException');
        FilterFactory::monthAndDay(12, 32);
    }

    public function testMonthDayInvalidDayForMonth()
    {
        $this->setExpectedException('\OutOfBoundsException');
        FilterFactory::monthAndDay(2, 30);
    }

    public function testMonthDayFalse()
    {
        $isChristmas = FilterFactory::monthAndDay(12, 25);
        $this->assertFalse($isChristmas(new \DateTime('December 26')));
    }

    public function testMonthDayTrue()
    {
        $isChristmas = FilterFactory::monthAndDay(12, 25);
        $this->assertTrue($isChristmas(new \DateTime('December 25')));
    }

    public function testNthDayOfWeekOfMonthNonInteger()
    {
        $this->setExpectedException('\InvalidArgumentException');
        FilterFactory::nthDayOfWeekOfMonth(1, 0, '1');
    }

    public function testNthDayOfWeekOfMonthInvalidN()
    {
        $this->setExpectedException('\OutOfBoundsException');
        FilterFactory::nthDayOfWeekOfMonth(6, 0, 1);
    }

    public function testNthDayOfWeekOfMonthInvalidDayOfWeek()
    {
        $this->setExpectedException('\OutOfBoundsException');
        FilterFactory::nthDayOfWeekOfMonth(1, 7, 1);
    }

    public function testNthDayOfWeekOfMonthInvalidMonth()
    {
        $this->setExpectedException('\OutOfBoundsException');
        FilterFactory::nthDayOfWeekOfMonth(1, 1, 13);
    }

    public function testNthDayOfWeekOfMonthFalseMonth()
    {
        $isThanksgiving = FilterFactory::nthDayOfWeekOfMonth(4, 4, 11);
        $this->assertFalse($isThanksgiving(new \DateTime('October 26 2015')));
    }

    public function testNthDayOfWeekOfMonthFalseWeek()
    {
        $isThanksgiving = FilterFactory::nthDayOfWeekOfMonth(4, 4, 11);
        $this->assertFalse($isThanksgiving(new \DateTime('November 19 2015')));
    }

    public function testNthDayOfWeekOfMonthFalseDayOfWeek()
    {
        $isThanksgiving = FilterFactory::nthDayOfWeekOfMonth(4, 4, 11);
        $this->assertFalse($isThanksgiving(new \DateTime('November 25 2015')));
    }

    public function testNthDayOfWeekOfMonthTrue()
    {
        $isThanksgiving = FilterFactory::nthDayOfWeekOfMonth(4, 4, 11);
        $this->assertTrue($isThanksgiving(new \DateTime('November 26 2015')));
    }

    public function testEasterMondayWrongYear()
    {
        $this->assertFalse(StaticFilter::isEasterMonday(new \DateTime('April 6 2014')));
    }

    public function testEasterMondayWrongMonth()
    {
        $this->assertFalse(StaticFilter::isEasterMonday(new \DateTime('March 6 2015')));
    }

    public function testEasterMondayWrongDay()
    {
        $this->assertFalse(StaticFilter::isEasterMonday(new \DateTime('April 5 2015')));
    }

    public function testEasterMondayMatch()
    {
        $this->assertTrue(StaticFilter::isEasterMonday(new \DateTime('April 6 2015 23:59:59')));
    }

    public function testGoodFridayWrongYear()
    {
        $this->assertFalse(StaticFilter::isGoodFriday(new \DateTime('April 3 2014')));
    }

    public function testGoodFridayWrongMonth()
    {
        $this->assertFalse(StaticFilter::isGoodFriday(new \DateTime('March 3 2015')));
    }

    public function testGoodFridayWrongDay()
    {
        $this->assertFalse(StaticFilter::isGoodFriday(new \DateTime('April 4 2015')));
    }

    public function testGoodFridayMatch()
    {
        $this->assertTrue(StaticFilter::isGoodFriday(new \DateTime('April 3 2015 23:59:59')));
    }
}
