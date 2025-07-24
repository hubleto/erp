<?php

namespace HubletoApp\Community\Calendar;

class CalendarManager
{
  public \HubletoMain\Loader $main;

  /** @var array<string, \Hubleto\Framework\Calendar> */
  protected array $calendars = [];

  public function __construct(\HubletoMain\Loader $main)
  {
    $this->main = $main;
  }

  public function addCalendar(string $source, string $color, string $calendarClass): void
  {
    $calendar = $this->main->di->create($calendarClass);
    $calendar->setColor($color);
    if ($calendar instanceof \Hubleto\Framework\Calendar) {
      $this->calendars[$source] = $calendar;
    }
  }

  /** @return array<string, \Hubleto\Framework\Calendar> */
  public function getCalendars(): array
  {
    return $this->calendars;
  }

  public function getCalendar(string $calendarClass): \Hubleto\Framework\Calendar
  {
    return $this->calendars[$calendarClass];
  }


}
