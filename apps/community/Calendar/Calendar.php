<?php

namespace HubletoApp\Community\Calendar;

use HubletoApp\Community\Calendar\Models\Activity;

class Calendar extends \HubletoMain\Core\Calendar {

  public array $activitySelectorConfig = [
    "addNewActivityButtonText" => "Add a simple event",
    "formComponent" => "CalendarActivityForm",
  ];

  public function prepareLoadActivitiesQuery(\HubletoApp\Community\Calendar\Models\Activity $mActivity, string $dateStart, string $dateEnd, array $filter = []): mixed
  {
    $query = $mActivity->record->prepareReadQuery()
      ->select($mActivity->table.".*", "activity_types.color", "activity_types.name as activity_type")
      ->leftJoin("activity_types", "activity_types.id", "=", $mActivity->table.".id_activity_type")
      ->where($mActivity->table.".date_start", ">=", $dateStart)
      ->where($mActivity->table.".date_start", "<=", $dateEnd)
    ;

    if (isset($filter['completed'])) $query = $query->where('completed', $filter['completed']);
    if (isset($filter['all_day'])) $query = $query->where('all_day', $filter['all_day']);

    return $query;
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    return $this->convertActivitiesToEvents(
      'calendar',
      $this->prepareLoadActivitiesQuery(new Activity($this->main), $dateStart, $dateEnd, $filter)->get()?->toArray(),
      function(array $activity) { return ''; }
    );
  }

}