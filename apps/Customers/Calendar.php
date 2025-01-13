<?php

namespace HubletoApp\Customers;

use HubletoApp\Customers\Models\CompanyActivity;

class Calendar extends \HubletoMain\Core\Calendar {

  public function loadEvents(array $params = []): array
  {

    $idCompany = (int) $params["idCompany"];
    $dateStart = date("Y-m-d H:i:s", strtotime((string) $this->main->params["start"]));
    $dateEnd = date("Y-m-d H:i:s", strtotime((string) $this->main->params["end"]));

    $mCompanyActivity = new CompanyActivity($this->main);

    $activities = $mCompanyActivity->eloquent
      ->select("company_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->leftJoin("activity_types", "activity_types.id", "=", "company_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idCompany > 0) $activities = $activities->where("id_company", $idCompany);

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