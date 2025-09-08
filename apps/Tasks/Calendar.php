<?php

namespace Hubleto\App\Community\Tasks;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Tasks",
    "addNewActivityButtonText" => "Add new activity linked to Tasks",
    "icon" => "fas fa-calendar",
    "formComponent" => "TasksFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
