<?php

namespace HubletoApp\Community\Leads;

class Calendar extends \HubletoMain\Core\Calendar {

  public array $activitySelectorConfig = [
    "title" => "Lead",
    "formComponent" => "LeadsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd): array
  {

    $idLead = $this->main->urlParamAsInteger('idLead');

    $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);

    $activities = $mLeadActivity->eloquent
      ->select("lead_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->with('LEAD.CUSTOMER')
      ->leftJoin("activity_types", "activity_types.id", "=", "lead_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idLead > 0) $activities = $activities->where("id_lead", $idLead);

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
      $events[$key]['color'] = $this->main->apps->community('Leads')->configAsString('calendarColor');
      $events[$key]['type'] = $activity->activity_type;
      $events[$key]['url'] = 'leads/' . $activity->id_lead;
      $events[$key]['category'] = 'lead';
      $events[$key]['details'] = 'Lead #' . $activity->LEAD->identifier . ' for ' . $activity->LEAD->CUSTOMER->name;
      
    }

    return $events;
  }

}