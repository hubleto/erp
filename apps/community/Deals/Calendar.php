<?php

namespace HubletoApp\Community\Deals;

class Calendar extends \HubletoApp\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Deals",
    "addNewActivityButtonText" => "Add new activity linked to deal",
    "icon" => "fas fa-handshake",
    "formComponent" => "DealsFormActivity"
  ];

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery(new Models\DealActivity($this->main), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = []): array
  {
    $idDeal = $this->main->urlParamAsInteger('idDeal');
    $mDealActivity = $this->main->di->create(Models\DealActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mDealActivity, $dateStart, $dateEnd, $filter)->with('DEAL.CUSTOMER');
    if ($idDeal > 0) {
      $activities = $activities->where("id_deal", $idDeal);
    }

    $events = $this->convertActivitiesToEvents(
      'deals',
      $activities->get()?->toArray(),
      function (array $activity) {
        if (isset($activity['DEAL'])) {
          $deal = $activity['DEAL'];
          $customer = $deal['CUSTOMER'] ?? [];
          return 'Deal ' . $deal['identifier'] . ' ' . $deal['title'] . (isset($customer['name']) ? ', ' . $customer['name'] : '');
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}
