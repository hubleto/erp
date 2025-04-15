<?php

namespace HubletoApp\Community\Deals;

class Calendar extends \HubletoMain\Core\Calendar {

  public array $activitySelectorConfig = [
    "title" => "Deal",
    "formComponent" => "SalesDealsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd): array
  {
    $idDeal = $this->main->urlParamAsInteger('idDeal');

    $mDealActivity = new \HubletoApp\Community\Deals\Models\DealActivity($this->main);

    $activities = $mDealActivity->eloquent
      ->select("deal_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->with('DEAL.CUSTOMER')
      ->leftJoin("activity_types", "activity_types.id", "=", "deal_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idDeal > 0) $activities = $activities->where("id_deal", $idDeal);

    $activities = $activities->get();
    $events = [];

    foreach ($activities as $key => $activity) { //@phpstan-ignore-line

      $dStart = (string) $activity->date_start;
      $tStart = (string) $activity->time_start;
      $dEnd = (string) $activity->date_end;
      $tEnd = (string) $activity->time_end;

      $events[$key]['id'] = $activity->id;

      if ($tStart != '') $events[$key]['start'] = $dStart . " " . $tStart;
      else $events[$key]['start'] = $dStart;

      if ($dEnd != '') {
        if ($tEnd != '') $events[$key]['end'] = $dEnd . " " . $tEnd;
        else $events[$key]['end'] = $dEnd;
      } else if ($tEnd != '') {
        $events[$key]['end'] = $dStart . " " . $tEnd;
      }

      //fix for fullCalendar not showing the last date of an event longer than one day
      if ((!empty($dStart) && !empty($dEnd) && (strtotime($dEnd) > strtotime($dStart)))) {
        if (empty($tEnd) || empty($tStart)) $events[$key]['end'] = date("Y-m-d", strtotime("+ 1 day", strtotime($dEnd)));
      }

      $events[$key]['allDay'] = $activity->all_day == 1 || $tStart == '' ? true : false;
      $events[$key]['title'] = $activity->subject;
      $events[$key]['backColor'] = $activity->color;
      $events[$key]['color'] = $this->main->apps->community('Deals')->configAsString('calendarColor');
      $events[$key]['type'] = $activity->activity_type;
      $events[$key]['url'] = 'deals/' . $activity->id_lead;
      $events[$key]['category'] = 'deal';
      $events[$key]['details'] = 'Deal #' . $activity->DEAL->identifier . ' for ' . $activity->DEAL->CUSTOMER->name;
    }

    return $events;
  }

}