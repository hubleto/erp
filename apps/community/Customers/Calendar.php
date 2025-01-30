<?php

namespace HubletoApp\Community\Customers;

use HubletoApp\Community\Customers\Models\CompanyActivity;

class Calendar extends \HubletoMain\Core\Calendar {

  public function loadEvents(): array
  {
    $idCompany = 0;
    $dateStart = '';
    $dateEnd = '';

    if ($this->main->isUrlParam('idCompany')) $idCompany = $this->main->urlParamAsInteger('idCompany');
    else return [];

    if ($this->main->isUrlParam("start") && $this->main->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("end")));
    } else {
      $dateStart = date("Y-m-d H:i:s");
      $dateEnd = date("Y-m-d H:i:s", strtotime("tommorow"));
    }

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

      $events[$key]['allDay'] = $activity->all_day == 1 || $tStart == null ? true : false;
      $events[$key]['title'] = $activity->subject;
      $events[$key]['backColor'] = $activity->color;
      $events[$key]['color'] = $activity->color;
      $events[$key]['type'] = $activity->activity_type;
    }

    return $events;
  }

}