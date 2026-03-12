<?php

namespace Hubleto\App\Community\Campaigns\Controllers;

use Hubleto\App\Community\Campaigns\Models\Campaign;
use Hubleto\App\Community\Campaigns\Models\Click;
use Hubleto\App\Community\Campaigns\Models\RecipientStatus;
use Hubleto\App\Community\Campaigns\Lib;

class Unsubscribe extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;
  public bool $requiresAuthenticatedUser = false;

  public function prepareView(): void
  {
    $urlDataB64 = $this->router()->urlParamAsString('c');
    $email = $this->router()->urlParamAsString('email');

    $urlDataJson = @base64_decode($urlDataB64);
    $urlData = @json_decode($urlDataJson, true);

    $campaignUid = $urlData['cuid'] ?? '';
    $idRecipient = (int) ($urlData['rcid'] ?? 0);

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Recipient */
    $mRecipientStatus = $this->getModel(RecipientStatus::class);

    $campaign = $mCampaign->record->where('uid', $campaignUid)->with('MAIL_TEMPLATE')->first();

    if (!empty($email) && $campaign) {
      $mRecipientStatus->record->create([
        'email' => $email,
        'is_unsubscribed' => true,
      ]);
    }

    $this->viewParams['campaign'] = $campaign;
    $this->viewParams['email'] = $email;
    $this->viewParams['c'] = $urlDataB64;

    $this->setView('@Hubleto:App:Community:Campaigns/Unsubscribe.twig');

  }

}
