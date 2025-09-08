<?php

namespace Hubleto\App\Community\Deals\Controllers;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'deals', 'content' => $this->translate('Deals') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->router()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->router()->urlParamAsString('calendarColor');

      /** @var \Hubleto\App\Community\Deals\Loader $dealsApp */
      $dealsApp = $this->appManager()->getApp(\Hubleto\App\Community\Deals\Loader::class);

      $dealsApp->setConfigAsString('calendarColor', $calendarColor);
      $dealsApp->saveConfig('calendarColor', $calendarColor);

      $dealPrefix = $this->router()->urlParamAsString('dealPrefix');
      $dealsApp->setConfigAsString('dealPrefix', $dealPrefix);
      $dealsApp->saveConfig('dealPrefix', $dealPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@Hubleto:App:Community:Deals/Settings.twig');
  }

}
