<?php

namespace HubletoApp\Community\CalendarActivities;

class Calendar extends \HubletoApp\Community\Calendar\Calendar {

  public array $calendarConfig = [
    "title" => "CalendarActivities",
    "addNewActivityButtonText" => "Add new activity linked to CalendarActivities",
    "icon" => "fas fa-calendar",
    "formComponent" => "CalendarActivitiesFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    return [];
  }

}