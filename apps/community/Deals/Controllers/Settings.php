<?php

namespace HubletoApp\Community\Deals\Controllers;

class Settings extends \HubletoMain\Core\Controller
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
      $showMostValuableDealsInDashboard = $this->main->urlParamAsBool('showMostValuableDealsInDashboard');
      $this->hubletoApp->setConfigAsBool('showMostValuableDealsInDashboard', $showMostValuableDealsInDashboard);
      $this->hubletoApp->saveConfig('showMostValuableDealsInDashboard', $showMostValuableDealsInDashboard ? '1' : '0');

      $showDealValueByStatusInDashboard = $this->main->urlParamAsBool('showDealValueByStatusInDashboard');
      $this->hubletoApp->setConfigAsBool('showDealValueByStatusInDashboard', $showDealValueByStatusInDashboard);
      $this->hubletoApp->saveConfig('showDealValueByStatusInDashboard', $showDealValueByStatusInDashboard ? '1' : '0');

      $this->viewParams['settingsSaved'] = true;
    }

    $this->setView('@HubletoApp:Community:Deals/Settings.twig');
  }

}