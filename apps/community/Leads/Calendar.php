<?php

namespace HubletoApp\Community\Leads;

class Calendar extends \HubletoMain\Core\Calendar {

  public array $activitySelectorConfig = [
    "addNewActivityButtonText" => "Add new activity linked to lead",
    "icon" => "fas fa-people-arrows",
    "formComponent" => "LeadsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd): array
  {

    $idLead = $this->main->urlParamAsInteger('idLead');

    $mLeadActivity = new \HubletoApp\Community\Leads\Models\LeadActivity($this->main);

    $activities = $mLeadActivity->record
      ->select("lead_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->with('LEAD.CUSTOMER')
      ->leftJoin("activity_types", "activity_types.id", "=", "lead_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idLead > 0) $activities = $activities->where("id_lead", $idLead);

    $activities = $activities->get()?->toArray();

    $events = $this->convertActivitiesToEvents(
      'leads',
      $activities,
      function(array $activity) {
        if (isset($activity['LEAD'])) {
          $lead = $activity['LEAD'];
          return 'Lead #' . $lead['identifier'];
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}