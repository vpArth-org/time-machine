<?php

namespace Test;

use Arth\Util\TimeMachine;
use PHPUnit\Framework\TestCase;

class TimeMachineTest extends TestCase
{
  /** @var TimeMachine */
  private $tm;

  const FORMAT = 'Y-m-d H:i:s.u';

  protected function setUp()
  {
    $this->tm = new TimeMachine();
  }

  /**
   * @dataProvider dts
   */
  public function testDateFromTimestamp($ts, $date)
  {
    $this->assertEquals($date, TimeMachine::ts2date($ts)->format(self::FORMAT));
  }
  /**
   * @dataProvider dts
   */
  public function testTimestampFromDate($ts, $date)
  {
    $dt = \DateTime::createFromFormat(self::FORMAT, $date);
    $this->assertEquals($ts, TimeMachine::date2ts($dt));
  }

  public function dts()
  {
    return [
      ['-128649659.999998', '1965-12-03 23:59:00.000002'],
      ['-128649660.000002', '1965-12-03 23:58:59.999998'],
      ['1544639767.999998', '2018-12-12 18:36:07.999998'],
    ];
  }

  public function testFreezedMode()
  {
    $date = '1965-12-03 23:59:59.000000';
    $this->tm->setFreezedMode(true);
    $this->tm->setNow(new \DateTime($date));
    usleep(100);
    $this->assertEquals($date, $this->tm->getNow()->format(self::FORMAT));
  }
  public function testUnfreezedMode()
  {
    $date = '1965-12-03 23:59:00.000000';
    $this->tm->setFreezedMode(false);
    $this->tm->setNow(new \DateTime($date));
    usleep(100);
    $this->assertNotEquals($date, $this->tm->getNow()->format(self::FORMAT));
  }

  public function testAkaSingletones()
  {
    $tm = TimeMachine::getInstance('some');
    $this->assertSame($tm, TimeMachine::getInstance('some'));
    $this->assertNotSame($tm, TimeMachine::getInstance('other'));
  }
}
