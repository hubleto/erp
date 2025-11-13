<?php

namespace Hubleto\App\Community\Calendar;


use Hubleto\App\Community\Calendar\Models\Activity;

class Calendar extends \Hubleto\Erp\Calendar
{
  public array $calendarConfig = [
    "title" => "Default",
    "addNewActivityButtonText" => "Add a simple event",
    "formComponent" => "CalendarActivityForm",
  ];

  public function prepareLoadActivityQuery(\Hubleto\App\Community\Calendar\Models\Activity $mActivity, int $id): mixed
  {
    return $mActivity->record->prepareReadQuery()->where("{$mActivity->table}.id", $id);
  }

  public function prepareLoadActivitiesQuery(\Hubleto\App\Community\Calendar\Models\Activity $mActivity, string $dateStart, string $dateEnd, array $filter = []): mixed
  {
    $dates = [];
    $tsStart = strtotime($dateStart);
    $tsEnd = strtotime($dateEnd);
    for ($ts = $tsStart; $ts < $tsEnd; $ts += 24*60*60) {
      $dates[] = date('Y-m-d', $ts);
    }

    $query = $mActivity->record->prepareReadQuery()
      ->with('ACTIVITY_TYPE')
    ;

    $query->where(function($q1) use ($mActivity, $dateStart, $dateEnd, $dates) {
      $q1->orWhere(function ($q2) use ($mActivity, $dateStart, $dateEnd) {
        $q2->whereRaw("
            (`{$mActivity->table}`.`date_start` >= ? AND `{$mActivity->table}`.`date_start` <= ?)
            OR (`{$mActivity->table}`.`date_end` >= ? AND `{$mActivity->table}`.`date_end` <= ?)
            OR (`{$mActivity->table}`.`date_start` <= ? AND `{$mActivity->table}`.`date_end` >= ?)
          ",
          [$dateStart, $dateEnd, $dateStart, $dateEnd, $dateStart, $dateEnd]
        );
      });
      foreach ($dates as $date) {
        $q1->orWhereRaw('JSON_CONTAINS(`' . $mActivity->table . '`.`recurrence`, \'"' . $date . '"\', "$.dates")');
      }
    });

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
          $query = $query->where($mActivity->table.".id_owner", $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId());
          break;
      }
    }

    return $query;
  }

  public function convertActivitiesToEvents(string $source, array $activities, \Closure $detailsCallback): array
  {
    $events = [];
    $eventKey = 0;

    foreach ($activities as $key => $activity) { //@phpstan-ignore-line
      $recurrence = @json_decode($activity['recurrence'], true);

      if (is_array($recurrence) && is_array($recurrence['dates'])) {
        $dates = $recurrence['dates'];
      } else {
        $dates = [ $activity['date_start'] ];
      }
      foreach ($dates as $date) {
        $dStart = date("Y-m-d", strtotime($date));
        $tStart = (string) ($activity['time_start'] ?? '');
        $dEnd = date("Y-m-d", strtotime($date));
        $tEnd = (string) ($activity['time_end'] ?? '');

        $events[$eventKey]['id'] = (int) ($activity['id'] ?? 0);

        if ($tStart != '') {
          $events[$eventKey]['start'] = $dStart . " " . $tStart;
        } else {
          $events[$eventKey]['start'] = $dStart;
        }

        if ($dEnd != '') {
          if ($tEnd != '') {
            $events[$eventKey]['end'] = $dEnd . " " . $tEnd;
          } else {
            $events[$eventKey]['end'] = $dEnd;
          }
        } elseif ($tEnd != '') {
          $events[$eventKey]['end'] = $dStart . " " . $tEnd;
        }

        $longerThanDay = (!empty($dStart) && !empty($dEnd) && ($dStart != $dEnd));

        // fix for fullCalendar not showing the last date of an event longer than one day
        if ((!empty($dStart) && !empty($dEnd) && $longerThanDay)) {
          $events[$eventKey]['end'] = date("Y-m-d", strtotime("+ 1 day", strtotime($dEnd)));
        }

        $events[$eventKey]['allDay'] = ($activity['all_day'] ?? 0) == 1 || $tStart == null ? true : false || $longerThanDay;
        $events[$eventKey]['title'] = (string) ($activity['subject'] ?? '');
        $events[$eventKey]['backColor'] = (string) ($activity['color'] ?? '');
        $events[$eventKey]['color'] = $this->color;
        $events[$eventKey]['type'] = (int) ($activity['activity_type'] ?? 0);
        $events[$eventKey]['source'] = $source; //'customers';
        $events[$eventKey]['details'] = $detailsCallback($activity);
        $events[$eventKey]['id_owner'] = $activity['id_owner'] ?? 0;
        $events[$eventKey]['owner'] = $activity['_LOOKUP[id_owner]'] ?? '';
        $eventKey++;
      }
    }

    return $events;
  }

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\Activity::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    return $this->convertActivitiesToEvents(
      'calendar',
      $this->prepareLoadActivitiesQuery($this->getService(Activity::class), $dateStart, $dateEnd, $filter)->get()?->toArray(),
      function (array $activity) { return ''; }
    );
  }

}
