<?php

namespace HubletoApp\Community\Leads;

class Calendar extends \HubletoApp\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Leads",
    "addNewActivityButtonText" => "Add new activity linked to lead",
    "icon" => "fas fa-people-arrows",
    "formComponent" => "LeadsFormActivity"
  ];

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery(new Models\LeadActivity($this->main), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = [], $idUser = 0): array
  {
    $idLead = $this->main->urlParamAsInteger('idLead');
    $mLeadActivity = $this->main->di->create(Models\LeadActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mLeadActivity, $dateStart, $dateEnd, $filter)->with('LEAD.CUSTOMER');
    if ($idLead > 0) {
      $activities = $activities->where("id_lead", $idLead);
    }

    $events = $this->convertActivitiesToEvents(
      'leads',
      $activities->get()?->toArray(),
      function (array $activity) {
        if (isset($activity['LEAD'])) {
          $lead = $activity['LEAD'];
          $customer = $lead['CUSTOMER'] ?? [];
          return 'Lead ' . $lead['identifier'] . ' ' . $lead['title'] . (isset($customer['name']) ? ', ' . $customer['name'] : '');
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}
