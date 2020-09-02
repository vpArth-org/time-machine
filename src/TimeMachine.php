<?php

namespace Arth\Util;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class TimeMachine implements TimeMachineInterface
{
  /** @var DateTimeInterface */
  protected $dt;
  /** @var float unix timestamp with useconds */
  protected $lastSetTime;
  /** @var DateTimeZone */
  protected $tz;

  /** @var TimeMachine[] */
  protected static $instances = [];
  public static function getInstance($id = 'default'): TimeMachine
  {
    return self::$instances[$id] ??
        (static::$instances[$id] = new static);
  }

  public function setNow(?DateTimeInterface $now = null): void
  {
    $this->dt = $now;
    if ($this->lastSetTime) {
      $this->lastSetTime = microtime(true);
    }
  }
  /**
   * @inheritDoc
   */
  public function getNow(): DateTimeInterface
  {
    $now = $this->calcNow();

    if (!$this->lastSetTime) {
      return $now;
    }
    $realNow           = microtime(true);
    $diff              = $realNow - $this->lastSetTime;
    $this->lastSetTime = $realNow;

    return self::ts2date((int)(self::date2ts($now)) + $diff, $this->tz);
  }

  /** @inheritDoc */
  public function setFrozenMode(bool $mode = true): void { $this->lastSetTime = $mode ? null : microtime(true); }

  public static function ts2date($ts, ?DateTimeZone $tz = null): DateTimeInterface
  {
    $dts = floor($ts);                             // integer part
    $uts = number_format($ts - $dts, 6, '.', '');  // fraction part

    return DateTimeImmutable::createFromFormat('U\+0.u', "$dts+$uts", $tz);
  }
  public static function date2ts(DateTimeInterface $dt): string
  {
    $dts = (int) $dt->format('U');
    $uts = (float) $dt->format('0.u');
    return number_format($dts + $uts, 6, '.', '');
  }

  public function getTz(): ?DateTimeZone{return $this->tz; }
  public function setTz(?DateTimeZone $tz = null): void{$this->tz = $tz; }

  protected function calcNow(): DateTimeInterface
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    return $this->dt ?? new DateTimeImmutable('now', $this->getTz());
  }
}
