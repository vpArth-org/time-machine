<?php

namespace Test;

use Arth\Util\TimeMachine;
use DateTimeZone;
use Generator;
use PHPUnit\Framework\TestCase;

class TimeMachineFromFormatTest extends TestCase
{

  protected function setUp(): void
  {
    date_default_timezone_set('UCT');
  }
  /**
   * @dataProvider data
   * @param                   $expectedTs
   * @param                   $strTime
   * @param string            $format
   * @param DateTimeZone|null $tz
   */
  public function testFromFormat($expectedTs, $strTime, $format = 'Y-m-d H:i:s', DateTimeZone $tz = null): void
  {
    $tm = TimeMachine::getInstance('test');
    $tm->setTz($tz);
    $tm->setNowFromFormatString($strTime, $format);

    self::assertEquals($expectedTs, $tm->getNow()->getTimestamp());
    self::assertEquals($strTime, $tm->getNow()->format($format));
  }

  public function data(): ?Generator
  {
    yield [-1504172684, '1922-05-03 14:15:16'];
    yield [-1504194284, '1922-05-03 14:15:16', 'Y-m-d H:i:s', new DateTimeZone('Asia/Novosibirsk')];
    yield [2538003599, '05.06.2050 015959 +01:00', 'd.m.Y His P'];
  }
}
