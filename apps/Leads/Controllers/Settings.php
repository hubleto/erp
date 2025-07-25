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

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->main->urlParamAsString('calendarColor');
      $this->hubletoApp->setConfigAsString('calendarColor', $calendarColor);
      $this->hubletoApp->saveConfig('calendarColor', $calendarColor);

      $leadPrefix = $this->main->urlParamAsString('leadPrefix');
      $this->hubletoApp->setConfigAsString('leadPrefix', $leadPrefix);
      $this->hubletoApp->saveConfig('leadPrefix', $leadPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Leads/Settings.twig');
  }

}
