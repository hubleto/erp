<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Home extends \HubletoMain\Core\Controllers\Controller
{

  public function init(): void
  {
    switch ($this->main->auth->getUserLanguage()) {
      case 'sk':
        $this->main->apps->community('Help')->addHotTip('sk/zakaznici/vytvorenie-noveho-kontaktu', 'Pridať nový kontakt');
      break;
    }
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $dashboardsApp = $this->main->apps->community('Dashboards');
    if ($dashboardsApp) {
      $mDashboard = new \HubletoApp\Community\Dashboards\Models\Dashboard($this->main);

      $defaultDashboard = $mDashboard->record->prepareReadQuery()
        ->where('is_default', true)
        ->with('PANELS')
        ->first()
        ?->toArray();
      ;

      $this->viewParams['defaultDashboard'] = $defaultDashboard;

    }

    $this->setView('@HubletoApp:Community:Desktop/Home.twig');
  }

}