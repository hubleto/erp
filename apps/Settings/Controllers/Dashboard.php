<?php

namespace HubletoApp\Settings\Controllers;

class Dashboard extends \HubletoCore\Core\Controller {


  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $this->viewParams['settings'] = $this->app->getSettings();

    $this->setView('@app/Settings/Views/Dashboard.twig');
  }

}