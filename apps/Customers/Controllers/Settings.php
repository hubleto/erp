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

    $settingsChanged = $this->getRouter()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->getRouter()->urlParamAsString('calendarColor');
      $customersApp = $this->getAppManager()->getApp(\HubletoApp\Community\Customers\Loader::class);
      $customersApp->setConfigAsString('calendarColor', $calendarColor);
      $customersApp->saveConfig('calendarColor', $calendarColor);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Customers/Settings.twig');
  }

}
