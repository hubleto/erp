<?php

namespace HubletoApp\Community\Deals;

class Calendar extends \HubletoMain\Core\Calendar {

  public function loadEvents(array $params = []): array
  {
    $idDeal = null;
    $dateStart = null;
    $dateEnd = null;
    if (isset($params["idDeal"])) $idDeal = (int) $params["idDeal"];
    else return [];

    if (isset($this->main->params["start"]) && isset($this->main->params["end"])) {
      $dateStart = date("Y-m-d H:i:s", strtotime((string) $this->main->params["start"]));
      $dateEnd = date("Y-m-d H:i:s", strtotime((string) $this->main->params["end"]));
    } else {
      $dateStart = date("Y-m-d H:i:s");
      $dateEnd = date("Y-m-d H:i:s", strtotime("tommorow"));
    }

    $mDealActivity = new \HubletoApp\Community\Deals\Models\DealActivity($this->main);

    $activities = $mDealActivity->eloquent
      ->select("deal_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->leftJoin("activity_types", "activity_types.id", "=", "deal_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idDeal > 0) $activities = $activities->where("id_deal", $idDeal);

    $activities = $activities->get();
    $events = [];

    foreach ($activities as $key => $activity) {

      $events[$key]['id'] = $activity->id;
      if ($activity->time_start != null) $events[$key]['start'] = $activity->date_start." ".$activity->time_start;
      else $events[$key]['start'] = $activity->date_start;
      if ($activity->date_end != null) {
        if ($activity->time_end != null) $events[$key]['end'] = $activity->date_end." ".$activity->time_end;
        else $events[$key]['end'] = $activity->date_end;
      } else if ($activity->time_end != null) {
        $events[$key]['end'] = $activity->date_start." ".$activity->time_end;
      }

      $events[$key]['allDay'] = $activity->all_day == 1 || $activity->time_start == null ? true : false;
      $events[$key]['title'] = $activity->subject;
      $events[$key]['backColor'] = $activity->color;
      $events[$key]['color'] = $activity->color;
      $events[$key]['type'] = $activity->activity_type;
    }

    return $events;
  }

}