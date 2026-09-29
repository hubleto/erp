<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Lib;

class LaunchCampaign extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    Lib::scheduleMissingEmailsInCampaign($idCampaign);
    return ['status' => 'success'];
  }
}
