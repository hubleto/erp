<?php

namespace Hubleto\App\Community\EmailMarketing\Controllers;

use Hubleto\App\Community\EmailMarketing\Models\Email;
use Hubleto\App\Community\EmailMarketing\Models\EmailClick;

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

    $emailUid = $clickData['cuid'] ?? '';
    $idRecipient = (int) ($clickData['rcid'] ?? 0);
    $url = $clickData['url'] ?? '';
    $bot = (bool) ($clickData['bot'] ?? false);

    if (empty($emailUid)) return 'Unknown email.';

    /** @var Email */
    $mEmail = $this->getModel(Email::class);

    /** @var Click */
    $mEmailClick = $this->getModel(EmailClick::class);

    $email = $mEmail->record->where('uid', $emailUid)->first();

    $log = [
      'ua' => $userAgent,
      'ip' => $remoteIp,
      'met' => $requestMethod,
    ];
    if ($bot) $log['bot'] = true;

    $botScore = 0;

    if ($bot) $botScore += 3;

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
    
    if ($email->id) {
      $mEmailClick->record->recordCreate([
        'id_email' => $email->id,
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
      return 'Email not found.';
    }
  }

}
