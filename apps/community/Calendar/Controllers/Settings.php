<?php

namespace HubletoApp\Community\Calendar\Controllers;

class Settings extends \HubletoMain\Core\Controllers\Controller
{

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'calendar', 'content' => $this->translate('Calendar') ],
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $settingsChanged = $this->main->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $showEventsForTodayInDashboard = $this->main->urlParamAsBool('showEventsForTodayInDashboard');
      $this->hubletoApp->setConfigAsBool('showEventsForTodayInDashboard', $showEventsForTodayInDashboard);
      $this->hubletoApp->saveConfig('showEventsForTodayInDashboard', $showEventsForTodayInDashboard ? '1' : '0');

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Calendar/Settings.twig');
  }

}