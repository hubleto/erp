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
    $clickDataB64 = $this->router()->urlParamAsString('c');

    $clickDataJson = @base64_decode($clickDataB64);
    $clickData = @json_decode($clickDataJson, true);

    $campaignUid = $clickData['cuid'] ?? '';
    $idRecipient = (int) ($clickData['rcid'] ?? 0);
    $url = $clickData['url'] ?? '';

    if (empty($campaignUid) || empty($url) || $idRecipient <= 0) return 'Invalid click data.';

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Click */
    $mClick = $this->getModel(Click::class);

    $campaign = $mCampaign->record->where('uid', $campaignUid)->first();

    if ($campaign->id) {
      $mClick->record->recordCreate([
        'id_campaign' => $campaign->id,
        'id_recipient' => $idRecipient,
        'url' => $url,
        'datetime_clicked' => date('Y-m-d H:i:s'),
      ]);

      header('Location: ' . $url, 302);
      exit;
    } else {
      return 'Campaign not found.';
    }
  }

}
