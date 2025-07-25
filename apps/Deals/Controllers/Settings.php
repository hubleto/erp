<?php

namespace HubletoApp\Community\Deals\Controllers;

class Settings extends \HubletoMain\Controller
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

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->main->urlParamAsString('calendarColor');
      $this->hubletoApp->setConfigAsString('calendarColor', $calendarColor);
      $this->hubletoApp->saveConfig('calendarColor', $calendarColor);

      $dealPrefix = $this->main->urlParamAsString('dealPrefix');
      $this->hubletoApp->setConfigAsString('dealPrefix', $dealPrefix);
      $this->hubletoApp->saveConfig('dealPrefix', $dealPrefix);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Deals/Settings.twig');
  }

}
