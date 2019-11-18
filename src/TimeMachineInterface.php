<?php

namespace Arth\Util;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

interface TimeMachineInterface
{
  public static function getInstance($id = 'default'): TimeMachine;

  public function setNow(?DateTimeInterface $now = null): void;
  /** @return DateTimeImmutable|DateTime - depends on setNow provided values */
  public function getNow(): DateTimeInterface;
  /**
   * @param bool $mode
   *   - true(default): after each setNow() call, value will be returned by getter as is
   *   - false: getter will increment time by passed time from setNow call moment
   */

  public function setFrozenMode(bool $mode = true): void;

  /** @return DateTimeImmutable */
  public static function ts2date($ts, ?DateTimeZone $tz = null): DateTimeInterface;
  public static function date2ts(DateTimeInterface $dt): string;

  public function setTz(?DateTimeZone $tz = null): void;
  public function getTz(): ?DateTimeZone;
}
