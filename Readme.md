# TimeMachine
Module for application time control for testing purpose.  

Usage:

  - `composer req arth/time-machine`  
  - replace all `new DateTime('now')` and analogues with `Arth\Utils\TimeMachine::getInstance()->getNow()`    
  - in tests call `Arth\Utils\TimeMachine::getInstance()->setNow($dt)` with necessary $dt object for time shift.

## Changelog

v1.0.0
 - getNow()/setNow(DateTimeInterface)
 - setFreezedMode(bool) - is time tick between getNow() calls?
 - date2ts(DateTimeInterface)/ts2date($ts) with correct microseconds handling