<?php

namespace HubletoApp\Community\Calendar;

class CalendarManager
{

  public \HubletoMain $main;

  /** @var array<string, \HubletoMain\Core\Calendar> */
  protected array $calendars = [];

  public function __construct(\HubletoMain $main)
  {
    $this->main = $main;
  }

  public function addCalendar(string $source, string $color, string $calendarClass): void
  {
    $calendar = new $calendarClass($this->main);
    $calendar->setColor($color);
    if ($calendar instanceof \HubletoMain\Core\Calendar) {
      $this->calendars[$source] = $calendar;
    }
  }

  /** @return array<string, \HubletoMain\Core\Calendar> */
  public function getCalendars(): array
  {
    return $this->calendars;
  }

  public function getCalendar(string $calendarClass): \HubletoMain\Core\Calendar
  {
    return $this->calendars[$calendarClass];
  }


}