<?php

namespace HubletoApp\Community\Leads\Controllers;

class Settings extends \HubletoMain\Controller
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

    $settingsChanged = $this->getRouter()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->getRouter()->urlParamAsString('calendarColor');
      $leadsApp = $this->getAppManager()->getApp(\HubletoApp\Community\Leads\Loader::class);
      $leadsApp->setConfigAsString('calendarColor', $calendarColor);
      $leadsApp->saveConfig('calendarColor', $calendarColor);

      $leadPrefix = $this->getRouter()->urlParamAsString('leadPrefix');
      $leadsApp->setConfigAsString('leadPrefix', $leadPrefix);
      $leadsApp->saveConfig('leadPrefix', $leadPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Leads/Settings.twig');
  }

}
