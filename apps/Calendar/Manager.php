<?php

namespace HubletoApp\Community\Calendar;

class Manager extends \Hubleto\Framework\Core
{

  /** @var array<string, \Hubleto\Erp\Calendar> */
  protected array $calendars = [];

  public function addCalendar(\Hubleto\Framework\Interfaces\AppInterface $app, string $source, string $color, string $calendarClass): void
  {
    $calendar = $this->getService($calendarClass);
    $calendar->setColor($color);
    $calendar->setApp($app);
    if ($calendar instanceof \Hubleto\Erp\Calendar) {
      $this->calendars[$source] = $calendar;
    }
  }

  /** @return array<string, \Hubleto\Erp\Calendar> */
  public function getCalendars(): array
  {
    return $this->calendars;
  }

  public function getCalendar(string $calendarClass): \Hubleto\Erp\Calendar
  {
    return $this->calendars[$calendarClass];
  }

}
