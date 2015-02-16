<?php

namespace iansltx\BusinessDays\Test;

use iansltx\BusinessDays\FastForwarder;
use DateTimeImmutable as Immutable;
use DateTime as Mutable;
use DateInterval as Interval;
use iansltx\BusinessDays\StaticFilter;

class FastForwarderTest extends \PHPUnit_Framework_TestCase
{
    public function testTimeZoneSafety()
    {
        $ff = FastForwarder::createWithDays(10);
        $startDt = new Immutable();
        $expectedDt = $startDt->add(new Interval('P10D'));

        $this->assertEquals($expectedDt, $ff->exec($startDt));
    }

    public function testFebruary()
    {
        $ff = $this->addFilterSet1(FastForwarder::createWithDays(10));

        $endDt = $ff->exec(new Immutable('2015-02-12 09:00:00'));

        $this->assertEquals('2015-02-27 09:00:00', $endDt->format('Y-m-d H:i:s'));
        $this->assertInstanceOf('\DateTimeImmutable', $endDt);
    }

    public function testNovember()
    {
        $ff = $this->addFilterSet1(FastForwarder::createWithDays(10));

        $endDt = $ff->exec(new Mutable('2015-11-20 09:00:00'));

        $this->assertEquals('2015-12-07 09:00:00', $endDt->format('Y-m-d H:i:s'));
        $this->assertInstanceOf('\DateTime', $endDt);
    }

    public function testSkipWhenException()
    {
        $ff = FastForwarder::createWithDays(1);

        $this->setExpectedException('InvalidArgumentException');
        $ff->skipWhen(function (\DateTimeInterface $dt) {return 0;}, 'strict_typehints_ftw');
    }

    protected function addFilterSet1(FastForwarder $ff)
    {
        return $ff->skipWhenWeekend()
            ->skipWhen([StaticFilter::class, 'isWeekend'], 'weekend') // overwrites convenience method above
            ->skipWhenNthDayOfWeekOfMonth(3, 1, 2, 'presidents_day')
            ->skipWhenNthDayOfWeekOfMonth(4, 4, 11, 'thanksgiving')
            ->skipWhenMonthAndDay(1, 1) // test auto-naming
            ->skipWhen(function (\DateTimeInterface $dt) { // test providing a callable directly
            return $dt->format('m') == 12 && $dt->format('d') == 25;
        }, 'christmas');
    }
}
