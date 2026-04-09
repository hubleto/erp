<?php

namespace Hubleto\App\Community\Calendar;

class Manager extends \Hubleto\Erp\Core
{

  /** @var array<string, \Hubleto\Erp\Calendar> */
  protected array $calendars = [];

  public function addCalendar(\Hubleto\Framework\Interfaces\AppInterface $app, string $source, string $calendarClass): void
  {
    $calendar = $this->getService($calendarClass);
    $calendarConfig = $calendar->getCalendarConfig();
    $calendar->setColor($app->configAsString('calendarColor', $calendarConfig['color'] ?? '#000000'));
    $calendar->setApp($app);
    if ($calendar instanceof \Hubleto\Erp\Calendar) {
      $this->calendars[$source] = $calendar;
    }
  }

  /**
   * [Description for getCalendars]
   *
   * @return array
   * 
   */
  public function getCalendars(): array
  {
    return $this->calendars;
  }

  /**
   * [Description for getCalendarsSorted]
   *
   * @return array
   * 
   */
  public function getCalendarsSorted(): array
  {
    $calendars = $this->calendars;

    uasort($calendars, function($a, $b) {
      $cfgA = $a->getCalendarConfig();
      $cfgB = $b->getCalendarConfig();
      return $cfgA['position'] > $cfgB['position'];
    });

    return $calendars;
  }

  /**
   * [Description for getCalendar]
   *
   * @param string $calendarClass
   * 
   * @return \Hubleto\Erp\Calendar
   * 
   */
  public function getCalendar(string $calendarClass): \Hubleto\Erp\Calendar
  {
    return $this->calendars[$calendarClass];
  }

}
