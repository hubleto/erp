<?php

namespace Hubleto\App\Community\Customers\Controllers;

class Settings extends \Hubleto\Erp\Controller
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

    $settingsChanged = $this->router()->urlParamAsBool('settingsChanged');

    if ($settingsChanged) {
      $calendarColor = $this->router()->urlParamAsString('calendarColor');
      $customersApp = $this->appManager()->getApp(\Hubleto\App\Community\Customers\Loader::class);
      $customersApp->setConfigAsString('calendarColor', $calendarColor);
      $customersApp->saveConfig('calendarColor', $calendarColor);

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@Hubleto:App:Community:Customers/Settings.twig');
  }

}
