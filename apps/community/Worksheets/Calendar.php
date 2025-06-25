<?php

namespace HubletoApp\Community\Worksheets;

class Calendar extends \HubletoApp\Community\Calendar\Calendar {

  public array $calendarConfig = [
    "title" => "Worksheets",
    "addNewActivityButtonText" => "Add new activity linked to Worksheets",
    "icon" => "fas fa-calendar",
    "formComponent" => "WorksheetsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}