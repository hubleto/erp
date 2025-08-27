<?php

namespace HubletoApp\Community\Cloud\Controllers;

class Dashboard extends \Hubleto\Erp\Controller
{
  public function prepareView(): void
  {
    parent::prepareView();

    $premiumAccount = $this->getService(\HubletoApp\Community\Cloud\PremiumAccount::class);

    $premiumAccount->updatePremiumInfo();
    $premiumAccount->recalculateCredit();

    $currentCredit = $premiumAccount->getCurrentCredit();
    $this->viewParams['currentCredit'] = $currentCredit;

    $premiumInfo = $premiumAccount->getPremiumInfo();

    $mLog = $this->getModel(\HubletoApp\Community\Cloud\Models\Log::class);
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

    $this->viewParams['freeTrialInfo'] = $premiumAccount->getFreeTrialInfo();
    $this->viewParams['subscriptionInfo'] = $premiumAccount->getSubscriptionInfo();
    $this->viewParams['priceForThisMonth'] = $premiumAccount->getPrice($premiumInfo['activeUsers'], $premiumInfo['paidApps'], 0);

    $this->setView('@HubletoApp:Community:Cloud/Dashboard.twig');
  }

}
