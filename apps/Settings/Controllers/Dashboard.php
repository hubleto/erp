<?php

namespace Hubleto\App\Community\Settings\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      // [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['settings'] = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class)->getSettings();
    $this->setView('@Hubleto:App:Community:Settings/Dashboard.twig');
  }

}
