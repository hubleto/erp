<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

use Hubleto\App\Community\Campaigns\Models\Campaign;

class Tracker extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresAuthenticatedUser = false;

  public function render(): string
  {
    $campaignUid = $this->router()->urlParamAsString('cuid');
    $contactId = $this->router()->urlParamAsInteger('cnid');
    $url = $this->router()->urlParamAsString('url');

    // $mCampaign = $this->getModel(Campaign::class);
    // $campaign = $mCampaign->where('uid', $campaignUid)->get();

    // if ($campaign) {

    // }

    echo $url;
    exit;
  }

}
