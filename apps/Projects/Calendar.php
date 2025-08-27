<?php

namespace Hubleto\App\Community\Projects;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Projects",
    "addNewActivityButtonText" => "Add new activity linked to Projects",
    "icon" => "fas fa-calendar",
    "formComponent" => "ProjectsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
