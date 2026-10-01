<?php

namespace Hubleto\App\Custom\LinkedinMessages\Controllers\Api;

use Hubleto\App\Custom\LinkedinMessages\Client;

class OauthConnect extends \Hubleto\Erp\Controllers\ApiController
{
  public bool $hideDefaultDesktop = true;

  public function renderJson(): array
  {
    /** @var Client */
    $client = $this->getService(Client::class);

    if (!$client->isConfigured()) {
      $this->router()->redirectTo('linkedin-messages/settings?oauthError=' . urlencode($this->translate('Set the Client ID and Client secret first.')));
      exit;
    }

    $state = bin2hex(random_bytes(16));
    if (session_status() !== PHP_SESSION_ACTIVE) @session_start();
    $_SESSION['linkedin-messages']['oauthState'] = $state;
    $_SESSION['linkedin-messages']['oauthUser'] = $this->authProvider()->getUserId();

    header('Location: ' . $client->buildAuthorizationUrl($state), true, 302);
    exit;
  }

}
