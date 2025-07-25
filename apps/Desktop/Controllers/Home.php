<?php

namespace HubletoApp\Community\Desktop\Controllers;

class Home extends \HubletoMain\Controller
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
      $mDashboard = $this->main->di->create(\HubletoApp\Community\Dashboards\Models\Dashboard::class);

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
