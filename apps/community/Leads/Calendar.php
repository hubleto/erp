<?php

namespace HubletoApp\Community\Leads;

class Calendar extends \HubletoMain\Core\Calendar {

  public function loadEvents(array $params = []): array
  {
    $idLead = null;
    $dateStart = null;
    $dateEnd = null;
    if (isset($params["idLead"])) $idLead = (int) $params["idLead"];
    else return [];

    if ($this->main->isUrlParam("start") && $this->main->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("end")));
    } else {
      $dateStart = date("Y-m-d H:i:s");
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime("tommorow"));
    }

    $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);

    $activities = $mLeadActivity->eloquent
      ->select("lead_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->leftJoin("activity_types", "activity_types.id", "=", "lead_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idLead > 0) $activities = $activities->where("id_lead", $idLead);

    $activities = $activities->get();
    $events = [];

    foreach ($activities as $key => $activity) {

      $events[$key]['id'] = $activity->id;
      if ($activity->time_start != null) $events[$key]['start'] = (string) $activity->date_start . " " . (string) $activity->time_start;
      else $events[$key]['start'] = $activity->date_start;
      if ($activity->date_end != null) {
        if ($activity->time_end != null) $events[$key]['end'] = (string) $activity->date_end . " " . (string) $activity->time_end;
        else $events[$key]['end'] = (string) $activity->date_end;
      } else if ($activity->time_end != null) {
        $events[$key]['end'] = (string) $activity->date_start . " " . (string) $activity->time_end;
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