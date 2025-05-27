<?php

namespace HubletoApp\Community\Premium\Controllers;

class Premium extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $this->hubletoApp->updatePremiumInfo();

    $currentCredit = $this->hubletoApp->getCurrentCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $mLog = new \HubletoApp\Community\Premium\Models\Log($this->main);
    $this->viewParams['log'] = $mLog->record
      ->selectRaw('
        month(date) as month,
        year(date) as year,
        max(ifnull(active_users, 0)) as max_active_users,
        max(ifnull(paid_apps, 0)) as max_paid_apps
      ')
      ->orderBy('date', 'desc')
      ->groupByRaw('concat(year(date), month(date))')
      ->get()->toArray()
    ;

    $this->viewParams['freeTrialInfo'] = $this->hubletoApp->getFreeTrialInfo();

    $this->setView('@HubletoApp:Community:Premium/Premium.twig');
  }

}