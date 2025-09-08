<?php

namespace Hubleto\App\Community\Leads\Controllers;

class Settings extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'leads', 'content' => $this->translate('Leads') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->router()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->router()->urlParamAsString('calendarColor');
      $leadsApp = $this->appManager()->getApp(\Hubleto\App\Community\Leads\Loader::class);
      $leadsApp->setConfigAsString('calendarColor', $calendarColor);
      $leadsApp->saveConfig('calendarColor', $calendarColor);

      $leadPrefix = $this->router()->urlParamAsString('leadPrefix');
      $leadsApp->setConfigAsString('leadPrefix', $leadPrefix);
      $leadsApp->saveConfig('leadPrefix', $leadPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@Hubleto:App:Community:Leads/Settings.twig');
  }

}
