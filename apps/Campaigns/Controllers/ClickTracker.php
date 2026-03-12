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

    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    $clickDataJson = @base64_decode($clickDataB64);
    $clickData = @json_decode($clickDataJson, true);

    $campaignUid = $clickData['cuid'] ?? '';
    $idRecipient = (int) ($clickData['rcid'] ?? 0);
    $url = $clickData['url'] ?? '';
    $bot = (bool) ($clickData['bot'] ?? false);

    if (empty($campaignUid)) return 'Unknown campaign.';

    /** @var Campaign */
    $mCampaign = $this->getModel(Campaign::class);

    /** @var Click */
    $mClick = $this->getModel(Click::class);

    $campaign = $mCampaign->record->where('uid', $campaignUid)->first();

    $log = [
      'ua' => $userAgent,
      'ip' => $remoteIp,
      'met' => $requestMethod,
    ];
    if ($bot) $log['bot'] = true;

    $botScore = 0;

    if ($bot) $botScore++;

    // Most security scanners use 'HEAD' to check headers without loading the page.
    if ($requestMethod !== 'GET') $botScore++;

    // 2. Blacklist of known bot strings in User-Agent
    $botKeywords = [
      'bot', 'spider', 'crawl', 'slurp', 'clark-analyzer', 
      'headless', 'security', 'scanner', 'mimecast', 'barracuda'
    ];

    foreach ($botKeywords as $keyword) {
      if (stripos($userAgent, $keyword) !== false) $botScore++;
    }
    
    if ($campaign->id) {
      $mClick->record->recordCreate([
        'id_campaign' => $campaign->id,
        'id_recipient' => $idRecipient,
        'url' => $url,
        'datetime_clicked' => date('Y-m-d H:i:s'),
        'log' => json_encode($log),
        'bot_score' => $botScore,
      ]);

      if (!empty($url)) {
        header('Location: ' . $url, 302);
        exit;
      }

      return '';
    } else {
      return 'Campaign not found.';
    }
  }

}
