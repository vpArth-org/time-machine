<?php

namespace Test;

use Arth\Util\TimeMachine;
use DateTime;
use PHPUnit\Framework\TestCase;

class TimeMachineTest extends TestCase
{
  /** @var TimeMachine */
  private $tm;

  public const FORMAT = 'Y-m-d H:i:s.u';

  protected function setUp()
  {
    $this->tm = new TimeMachine();
  }

  /**
   * @dataProvider dts
   */
  public function testDateFromTimestamp($ts, $date): void
  {
    $this->assertEquals($date, TimeMachine::ts2date($ts)->format(self::FORMAT));
  }
  /**
   * @dataProvider dts
   */
  public function testTimestampFromDate($ts, $date): void
  {
    $dt = DateTime::createFromFormat(self::FORMAT, $date);
    $this->assertEquals($ts, TimeMachine::date2ts($dt));
  }

  public function dts(): array
  {
    return [
      ['-128649659.999998', '1965-12-03 23:59:00.000002'],
      ['-128649660.000002', '1965-12-03 23:58:59.999998'],
      ['1544639767.999998', '2018-12-12 18:36:07.999998'],
    ];
  }

  public function testFrozenMode(): void
  {
    $date = '1965-12-03 23:59:59.000000';
    $this->tm->setFrozenMode(true);
    $this->tm->setNow(new DateTime($date));
    usleep(100);
    $this->assertEquals($date, $this->tm->getNow()->format(self::FORMAT));
  }
  public function testUnfrozenMode(): void
  {
    $date = '1965-12-03 23:59:00.000000';
    $this->tm->setFrozenMode(false);
    $this->tm->setNow(new DateTime($date));
    usleep(0); // Schedule itself takes near 50us

    $later = $this->tm->getNow()->format(self::FORMAT);
    $this->assertGreaterThan($date, $later);
  }

  public function testAkaSingletons(): void
  {
    $subj = TimeMachine::getInstance('some');
    $this->assertSame($subj, TimeMachine::getInstance('some'));
    $this->assertNotSame($subj, TimeMachine::getInstance('other'));
  }
}
