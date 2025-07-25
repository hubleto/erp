<?php

namespace HubletoApp\Community\Calendar;

use HubletoApp\Community\Calendar\Models\Activity;

class Calendar extends \HubletoMain\Calendar
{
  public array $calendarConfig = [
    "title" => "Default",
    "addNewActivityButtonText" => "Add a simple event",
    "formComponent" => "CalendarActivityForm",
  ];

  public function prepareLoadActivityQuery(\HubletoApp\Community\Calendar\Models\Activity $mActivity, int $id): mixed
  {
    return $mActivity->record->prepareReadQuery()->where("{$mActivity->table}.id", $id);
  }

  public function prepareLoadActivitiesQuery(\HubletoApp\Community\Calendar\Models\Activity $mActivity, string $dateStart, string $dateEnd, array $filter = []): mixed
  {
    $query = $mActivity->record->prepareReadQuery()
      ->with('ACTIVITY_TYPE')
      ->where(function ($q) use ($mActivity, $dateStart, $dateEnd) {
        $q->whereRaw("
          ({$mActivity->table}.date_start >= '{$dateStart}' AND {$mActivity->table}.date_start <= '{$dateEnd}')
          OR ({$mActivity->table}.date_end >= '{$dateStart}' AND {$mActivity->table}.date_end <= '{$dateEnd}')
          OR ({$mActivity->table}.date_start <= '{$dateStart}' AND {$mActivity->table}.date_end >= '{$dateEnd}')
        ");
      })
    ;

    if (isset($filter['idUser']) && $filter['idUser'] > 0) {
      $query = $query->where($mActivity->table . '.id_owner', $filter['idUser']);
    }
    if (isset($filter['completed'])) {
      $query = $query->where('completed', $filter['completed']);
    }
    if (isset($filter['all_day'])) {
      $query = $query->where('all_day', $filter['all_day']);
    }
    if (isset($filter['fOwnership'])) {
      switch ($filter["fOwnership"]) {
        case 1:
          $query = $query->where($mActivity->table.".id_owner", $this->main->auth->getUserId());
          break;
      }
    }

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

      if ($tStart != '') {
        $events[$key]['start'] = $dStart . " " . $tStart;
      } else {
        $events[$key]['start'] = $dStart;
      }

      if ($dEnd != '') {
        if ($tEnd != '') {
          $events[$key]['end'] = $dEnd . " " . $tEnd;
        } else {
          $events[$key]['end'] = $dEnd;
        }
      } elseif ($tEnd != '') {
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
      $events[$key]['id_owner'] = $activity['id_owner'] ?? 0;
      $events[$key]['owner'] = $activity['_LOOKUP[id_owner]'] ?? '';
    }

    return $events;
  }

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery(new Models\Activity($this->main), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    return $this->convertActivitiesToEvents(
      'calendar',
      $this->prepareLoadActivitiesQuery(new Activity($this->main), $dateStart, $dateEnd, $filter)->get()?->toArray(),
      function (array $activity) { return ''; }
    );
  }

}
