<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers\Api;

use Hubleto\App\Community\EmailMarketing\Lib;
use Hubleto\App\Community\EmailMarketing\Models\Campaign;
use Hubleto\App\Community\EmailMarketing\Models\Email;

class CloseEmailInCampaign extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idCampaign = $this->router()->urlParamAsInteger('idCampaign');
    $idEmail = $this->router()->urlParamAsInteger('idEmail');

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);
    $campaign = $mCampaign->record->where('id', $idCampaign)->first();
    if (!$campaign) throw new \Exception("Campaign not found");

    /** @var Email */
    $mEmail = $this->getModel(Email::class);
    $email = $mEmail->record->where('id', $idEmail)->first();
    if (!$email) throw new \Exception("Email not found");

    $mEmail->record->where('id', $idEmail)->update(['is_closed' => true]);

    return ['status' => 'success'];
  }
}
