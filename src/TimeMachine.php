<?php

namespace Arth\Util;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

class TimeMachine
{
  /** @var DateTimeInterface */
  protected $dt = null;
  /** @var float unix timestamp with useconds */
  protected $lastSetTime = null;
  /** @var DateTimeZone */
  protected $tz;

  public function setNow(?DateTimeInterface $now = null)
  {
    $this->dt = $now;
    if ($this->lastSetTime) $this->lastSetTime = microtime(true);

    return $now;
  }
  /**
   * @return \DateTimeImmutable|\DateTime
   * @throws \Exception
   */
  public function getNow(): DateTimeInterface
  {
    $now = $this->dt ?: new DateTimeImmutable('now', $this->tz);
    if (!$this->lastSetTime) {
      return $now;
    }
    $realNow = microtime(true);
    $diff = $realNow - $this->lastSetTime;
    $this->lastSetTime = $realNow;

    return self::ts2date(self::date2ts($now)+$diff, $this->tz);
  }

  /**
   * @param bool $mode
   *   - true(default): each setted now value will be returned by getter as is
   *   - false: getter will increment time by passed time from setter call moment
   */
  public function setFreezedMode(bool $mode = true) { $this->lastSetTime = $mode ? null : microtime(true); }

  /** @var TimeMachine[] */
  protected static $instances = [];
  public static function getInstance($id = 'default')
  {
    return self::$instances[$id] ??
      (static::$instances[$id] = new static);
  }

  public static function ts2date($ts, ?DateTimeZone $tz = null)
  {
    $dts = floor($ts);                             // integer part
    $uts = number_format($ts - $dts, 6, '.', '');  // fraction part

    return DateTimeImmutable::createFromFormat('U\+0.u', "$dts+$uts", $tz);
  }
  public static function date2ts(DateTimeInterface $dt)
  {
    $dts = (int) $dt->format('U');
    $uts = (float) $dt->format('0.u');
    return number_format($dts + $uts, 6, '.', '');
  }
  public function getTz(): ?DateTimeZone{return $this->tz; }
  public function setTz(?DateTimeZone $tz = null): void{$this->tz = $tz; }
}
