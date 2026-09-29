<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Lib;

class LaunchEmailInCampaign extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idEmail = $this->router()->urlParamAsInteger('idEmail');
    Lib::scheduleMissingEmailsInCampaign($idCampaign, $idEmail);
    return ['status' => 'success'];
  }
}
