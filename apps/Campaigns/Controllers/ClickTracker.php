<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Click;

class ClickTracker extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresAuthenticatedUser = false;

  public function render(): string
  {
    $campaignUid = $this->router()->urlParamAsString('cuid');
    $contactId = $this->router()->urlParamAsInteger('cnid');
    $url = $this->router()->urlParamAsString('url');

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Click */
    $mClick = $this->getModel(Click::class);

    $campaign = $mCampaign->record->where('uid', $campaignUid)->first();

    if ($campaign->id) {
      $mClick->record->recordCreate([
        'id_campaign' => $campaign->id,
        'id_contact' => $contactId,
        'url' => $url,
        'datetime_clicked' => date('Y-m-d H:i:s'),
      ]);
    }

    header('Location: ' . $url, 302);
    exit;
  }

}
