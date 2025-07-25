<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Dashboard extends \HubletoMain\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $this->hubletoApp->updatePremiumInfo();
    $this->hubletoApp->recalculateCredit();

    $currentCredit = $this->hubletoApp->getCurrentCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $premiumInfo = $this->hubletoApp->getPremiumInfo();

    $mLog = $this->main->di->create(\HubletoApp\Community\Cloud\Models\Log::class);
    $this->viewParams['log'] = $mLog->record
      ->selectRaw('
        month(log_datetime) as month,
        year(log_datetime) as year,
        max(ifnull(active_users, 0)) as max_active_users,
        max(ifnull(paid_apps, 0)) as max_paid_apps
      ')
      ->orderBy('log_datetime', 'desc')
      ->groupByRaw('concat(year(log_datetime), month(log_datetime))')
      ->get()->toArray()
    ;

    $this->viewParams['freeTrialInfo'] = $this->hubletoApp->getFreeTrialInfo();
    $this->viewParams['subscriptionInfo'] = $this->hubletoApp->getSubscriptionInfo();
    $this->viewParams['priceForThisMonth'] = $this->hubletoApp->getPrice($premiumInfo['activeUsers'], $premiumInfo['paidApps'], 0);

    $this->setView('@HubletoApp:Community:Cloud/Dashboard.twig');
  }

}
