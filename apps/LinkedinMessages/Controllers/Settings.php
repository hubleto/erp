<?php

namespace Hubleto\App\Community\LinkedinMessages\Controllers;

use Hubleto\App\Community\LinkedinMessages\Client;
use Hubleto\App\Community\LinkedinMessages\Loader;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'linkedin-messages', 'content' => $this->translate('LinkedIn messages') ],
      [ 'url' => '', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Loader */
    $app = $this->appManager()->getApp(Loader::class);
    /** @var Client */
    $client = $this->getService(Client::class);

    if ($this->router()->urlParamAsBool('settingsChanged')) {
      foreach (['clientId', 'scopes', 'apiVersion'] as $key) {
        $value = trim($this->router()->urlParamAsString($key));
        $app->setConfigAsString($key, $value);
        $app->saveConfig($key, $value);
      }

      // the secret is only overwritten when a new one is entered
      $secret = trim($this->router()->urlParamAsString('clientSecret'));
      if ($secret !== '') {
        $app->setConfigAsString('clientSecret', $secret);
        $app->saveConfig('clientSecret', $secret);
      }

      $this->viewParams['settingsSaved'] = true;
    }

    $this->viewParams['oauthError'] = $this->router()->urlParamAsString('oauthError');
    $this->viewParams['app'] = $app;
    $this->viewParams['redirectUri'] = $client->getRedirectUri();
    $this->viewParams['defaultScopes'] = Client::DEFAULT_SCOPES;
    $this->viewParams['defaultApiVersion'] = Client::DEFAULT_API_VERSION;
    $this->viewParams['clientSecretIsSet'] = $app->configAsString('clientSecret') !== '';

    $this->setView('@Hubleto:App:Custom:LinkedinMessages/Settings.twig');
  }
}
