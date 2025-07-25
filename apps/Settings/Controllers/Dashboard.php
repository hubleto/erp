<?php

namespace HubletoApp\Community\Settings\Controllers;

class Dashboard extends \HubletoMain\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'settings', 'content' => $this->translate('Settings') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();
    $this->viewParams['settings'] = $this->main->apps->community('Settings')->getSettings();
    $this->setView('@HubletoApp:Community:Settings/Dashboard.twig');
  }

}
