<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Dashboard extends \HubletoMain\Core\Controllers\Controller {

  public function prepareView(): void
  {
    parent::prepareView();

    $this->hubletoApp->updatePremiumInfo();

    $this->hubletoApp->recalculateCredit();

    $currentCredit = $this->hubletoApp->getCurrentCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $mLog = new \HubletoApp\Community\Cloud\Models\Log($this->main);
    $this->viewParams['log'] = $mLog->record
      ->selectRaw('
        month(log_datetime) as month,
        year(log_datetime) as year,
        max(ifnull(active_users, 0)) as max_active_users,
        max(ifnull(paid_apps, 0)) as max_paid_apps,
        max(ifnull(price, 0)) as max_price
      ')
      ->orderBy('log_datetime', 'desc')
      ->groupByRaw('concat(year(log_datetime), month(log_datetime))')
      ->get()->toArray()
    ;

    $this->viewParams['freeTrialInfo'] = $this->hubletoApp->getFreeTrialInfo();

    $this->setView('@HubletoApp:Community:Cloud/Dashboard.twig');
  }

}