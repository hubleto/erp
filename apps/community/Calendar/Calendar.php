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
      ->with('ACTIVITY_TYPE')
      ->where($mActivity->table.".date_start", ">=", $dateStart)
      ->where($mActivity->table.".date_start", "<=", $dateEnd)
    ;

    if (isset($filter['completed'])) $query = $query->where('completed', $filter['completed']);
    if (isset($filter['all_day'])) $query = $query->where('all_day', $filter['all_day']);

    return $query;
  }

  public function convertActivitiesToEvents(string $source, array $activities, \Closure $detailsCallback): array
  {
    $events = [];

    foreach ($activities as $key => $activity) { //@phpstan-ignore-line

      $dStart = (string) ($activity['date_start'] ?? '');
      $tStart = (string) ($activity['time_start'] ?? '');
      $dEnd = (string) ($activity['date_end'] ?? '');
      $tEnd = (string) ($activity['time_end'] ?? '');

      $events[$key]['id'] = (int) ($activity['id'] ?? 0);

      if ($tStart != '') $events[$key]['start'] = $dStart . " " . $tStart;
      else $events[$key]['start'] = $dStart;

      if ($dEnd != '') {
        if ($tEnd != '') $events[$key]['end'] = $dEnd . " " . $tEnd;
        else $events[$key]['end'] = $dEnd;
      } else if ($tEnd != '') {
        $events[$key]['end'] = $dStart . " " . $tEnd;
      }

      $longerThanDay = (!empty($dStart) && !empty($dEnd) && ($dStart != $dEnd));

      // fix for fullCalendar not showing the last date of an event longer than one day
      if ((!empty($dStart) && !empty($dEnd) && $longerThanDay)) {
        $events[$key]['end'] = date("Y-m-d", strtotime("+ 1 day", strtotime($dEnd)));
      }

      $events[$key]['allDay'] = ($activity['all_day'] ?? 0) == 1 || $tStart == null ? true : false || $longerThanDay;
      $events[$key]['title'] = (string) ($activity['subject'] ?? '');
      $events[$key]['backColor'] = (string) ($activity['color'] ?? '');
      $events[$key]['color'] = $this->color;
      $events[$key]['type'] = (int) ($activity['activity_type'] ?? 0);
      $events[$key]['source'] = $source; //'customers';
      $events[$key]['details'] = $detailsCallback($activity);
      $events[$key]['owner'] = $activity['_LOOKUP[id_owner]'] ?? '';
    }

    return $events;
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