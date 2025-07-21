<?php

namespace HubletoApp\Community\Events;

class Calendar extends \HubletoApp\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Events",
    "addNewActivityButtonText" => "Add new activity linked to Events",
    "icon" => "fas fa-calendar",
    "formComponent" => "EventsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
