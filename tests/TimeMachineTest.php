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

  protected function setUp(): void
  {
    date_default_timezone_set('UCT');
    $this->tm = TimeMachine::getInstance();
  }

  /**
   * @dataProvider dts
   */
  public function testDateFromTimestamp($ts, $date): void
  {
    self::assertEquals($date, TimeMachine::ts2date($ts)->format(self::FORMAT));
  }
  /**
   * @dataProvider dts
   */
  public function testTimestampFromDate($ts, $date): void
  {
    $dt = DateTime::createFromFormat(self::FORMAT, $date);
    self::assertEquals($ts, TimeMachine::date2ts($dt));
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
    /** @noinspection PhpUnhandledExceptionInspection */
    $dt = new DateTime($date);
    $this->tm->setNow($dt);
    usleep(100);
    self::assertEquals($date, $this->tm->getNow()->format(self::FORMAT));
  }
  public function testUnfrozenMode(): void
  {
    $date = '1965-12-03 23:59:00.000000';
    $this->tm->setFrozenMode(false);
    /** @noinspection PhpUnhandledExceptionInspection */
    $dt = new DateTime($date);

    $this->tm->setNow($dt);
    usleep(0); // Schedule itself takes near 50us

    $later = $this->tm->getNow()->format(self::FORMAT);
    self::assertGreaterThan($date, $later);
  }

  public function testAkaSingletons(): void
  {
    $subj = TimeMachine::getInstance('some');
    self::assertSame($subj, TimeMachine::getInstance('some'));
    self::assertNotSame($subj, TimeMachine::getInstance('other'));
  }
}
