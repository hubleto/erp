<?php

namespace Hubleto\App\Community\AiAssistent\Controllers;

class Settings extends \Hubleto\Erp\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'ai-assistant', 'content' => 'AIAssistant' ],
      [ 'url' => 'settings', 'content' => 'Settings' ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->router()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $apiKey = $this->router()->urlParamAsString('apiKey');
      $sensitivityLevel = $this->router()->urlParamAsInteger('sensitivityLevel');
      $model = $this->router()->urlParamAsString('model');

      /** @var \Hubleto\App\Community\AiAssistent\Loader $aiApp */
      $aiApp = $this->appManager()->getApp(\Hubleto\App\Community\AiAssistent\Loader::class);

      $aiApp->setConfigAsString('apiKey', $apiKey);
      $aiApp->saveConfig('apiKey', $apiKey);
      
      $aiApp->setConfigAsInteger('sensitivityLevel', $sensitivityLevel);
      $aiApp->saveConfig('sensitivityLevel', $sensitivityLevel);

      $aiApp->setConfigAsString('model', $model);
      $aiApp->saveConfig('model', $model);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@Hubleto:App:Community:AiAssistent/Settings.twig');
  }

}
