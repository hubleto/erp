<?php

namespace HubletoApp\Community\Deals;

class Calendar extends \HubletoMain\Core\Calendar {

  public array $activitySelectorConfig = [
    "addNewActivityButtonText" => "Add new activity linked to deal",
    "icon" => "fas fa-handshake",
    "formComponent" => "DealsFormActivity"
  ];

  public function loadEvents(string $dateStart, string $dateEnd): array
  {
    $idDeal = $this->main->urlParamAsInteger('idDeal');

    $mDealActivity = new \HubletoApp\Community\Deals\Models\DealActivity($this->main);

    $activities = $mDealActivity->record
      ->select("deal_activities.*", "activity_types.color", "activity_types.name as activity_type")
      ->with('DEAL.CUSTOMER')
      ->leftJoin("activity_types", "activity_types.id", "=", "deal_activities.id_activity_type")
      ->where("date_start", ">=", $dateStart)
      ->where("date_start", "<=", $dateEnd)
    ;

    if ($idDeal > 0) $activities = $activities->where("id_deal", $idDeal);

    $activities = $activities->get()?->toArray();

    $events = $this->convertActivitiesToEvents(
      'deals',
      $activities,
      function(array $activity) {
        if (isset($activity['DEAL'])) {
          $deal = $activity['DEAL'];
          return 'Deal #' . $deal['identifier'];
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}