<?php

namespace HubletoApp\Community\OAuth;

class Calendar extends \HubletoApp\Community\Calendar\Calendar {

  public array $calendarConfig = [
    "title" => "OAuth",
    "addNewActivityButtonText" => "Add new activity linked to OAuth",
    "icon" => "fas fa-calendar",
    "formComponent" => "OAuthFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    // Implement your functionality for loading calendar events.

    return [];
  }

}