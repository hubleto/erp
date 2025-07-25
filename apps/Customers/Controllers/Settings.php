<?php

namespace HubletoApp\Community\Customers\Controllers;

class Settings extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'customers', 'content' => $this->translate('Customers') ],
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

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Customers/Settings.twig');
  }

}
