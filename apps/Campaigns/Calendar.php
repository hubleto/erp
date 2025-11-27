<?php

namespace Hubleto\App\Community\Campaigns;

class Calendar extends \Hubleto\App\Community\Calendar\Calendar
{
  public array $calendarConfig = [
    "title" => "Campaigns",
    "addNewActivityButtonText" => "Add new activity linked to campaign",
    "icon" => "fas fa-users-viewfinder",
    "formComponent" => "CampaignFormActivity"
  ];

  public function loadEvent(int $id): array
  {
    return $this->prepareLoadActivityQuery($this->getModel(Models\CampaignActivity::class), $id)->first()?->toArray();
  }

  public function loadEvents(string $dateStart, string $dateEnd, array $filter = [], $idUser = 0): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $mCampaignActivity = $this->getModel(Models\CampaignActivity::class);
    $activities = $this->prepareLoadActivitiesQuery($mCampaignActivity, $dateStart, $dateEnd, $filter)->with('CAMPAIGN');
    if ($idCampaign > 0) {
      $activities = $activities->where("id_campaign", $idCampaign);
    }

    $events = $this->convertActivitiesToEvents(
      'campaigns',
      $activities->get()?->toArray(),
      function (array $activity) {
        if (isset($activity['CAMPAIGN'])) {
          $campaign = $activity['CAMPAIGN'];
          return 'Campaign #' . $campaign['id'];
        } else {
          return '';
        }
      }
    );

    return $events;
  }

}
