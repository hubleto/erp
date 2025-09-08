<?php

namespace Hubleto\App\Community\EventRegistrations;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "EventRegistrations",
    "addNewActivityButtonText" => "Add new activity linked to EventRegistrations",
    "icon" => "fas fa-calendar",
    "formComponent" => "EventRegistrationsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = [], $idUser = 0): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}
