<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

use Hubleto\App\Community\Campaigns\Models\Campaign;

class Tracker extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresUserAuthentication = false;

  public function render(): string
  {
    $campaignUid = $this->getRouter()->urlParamAsString('cuid');
    $contactId = $this->getRouter()->urlParamAsInteger('cnid');
    $url = $this->getRouter()->urlParamAsString('url');

    // $mCampaign = $this->getModel(Campaign::class);
    // $campaign = $mCampaign->where('uid', $campaignUid)->get();

    // if ($campaign) {

    // }

    echo $url;
    exit;
  }

}
