<?php

namespace Hubleto\App\Community\Campaigns\Controllers\Api;


use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\CampaignActivity;

class LogActivity extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger("idCampaign");
    $activity = $this->router()->urlParamAsString("activity");
    if ($idCampaign > 0 && $activity != '') {
      /** @var Campaign */
      $mCampaign = $this->getModel(Campaign::class);
      $campaign = $mCampaign->record->find($idCampaign)->first()?->toArray();

      if ($campaign && $campaign['id'] > 0) {
        $mCampaignActivity = $this->getService(CampaignActivity::class);
        $mCampaignActivity->record->recordCreate([
          'id_campaign' => $idCampaign,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idCampaign" => $idCampaign,
    ];
  }

}
