<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class PayMonthly extends \HubletoMain\Core\Controllers\Controller {

  const PAYMENT_SUCCESS = 1;
  const THIS_IS_NOT_PREMIUM_ACCOUNT = 2;
  const SUBSCRIPTION_NOT_ACTIVE = 3;
  const FREE_TRIAL_PERIOD = 4;
  const THIS_MONTH_ALREADY_PAID = 5;

  public bool $requiresUserAuthentication = false;

  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    if (!$this->hubletoApp->premiumAccountActivated() > 0) {
      return [ 'result' => self::THIS_IS_NOT_PREMIUM_ACCOUNT ];
    }

    $subscriptionRenewalActive = $this->hubletoApp->configAsBool('subscriptionRenewalActive');
    $subscriptionActiveUntil = $this->hubletoApp->configAsString('subscriptionActiveUntil');
    $subscriptionActive = strtotime($subscriptionActiveUntil) > time();

    if (!$subscriptionActive && !$subscriptionRenewalActive) {
      return [ 'result' => self::SUBSCRIPTION_NOT_ACTIVE ];
    }

    $freeTrialPeriodUntil = $this->hubletoApp->configAsString('freeTrialPeriodUntil');

    if (strtotime($freeTrialPeriodUntil) > time()) {
      return [ 'result' => self::FREE_TRIAL_PERIOD ];
    }


    $mPayment = new \HubletoApp\Community\Cloud\Models\Payment($this->main);

    $prevPrevMonth = date('m', strtotime('-2 month'));
    $prevPrevYear = date('Y', strtotime('-2 month'));

    $prevMonth = date('m', strtotime('-1 month'));
    $prevYear = date('Y', strtotime('-1 month'));

    $thisMonth = date('m');
    $thisYear = date('Y');

    $paymentThisMonth = $mPayment->record->whereMonth('datetime_charged', $thisMonth)->whereYear('datetime_charged', $thisYear)->count();

    if ($paymentThisMonth > 0) {
      return [
        'result' => self::THIS_MONTH_ALREADY_PAID,
      ];
    } else {
      $premiumInfoPrevPrevMonth = $this->hubletoApp->getPremiumInfo($prevPrevMonth, $prevPrevYear);
      $premiumInfoPrevMonth = $this->hubletoApp->getPremiumInfo($prevMonth, $prevYear);
      $premiumInfoThisMonth = $this->hubletoApp->getPremiumInfo($thisMonth, $thisYear);

      // suma za pouzivatelov tento mesiac

      $fullPriceForCurrentlyActiveUsers = $this->hubletoApp->getPrice(
        $premiumInfoThisMonth['activeUsers'],
        $premiumInfoThisMonth['paidApps'],
        0
      );

      $discountedPriceForCurrentlyActiveUsers = $this->hubletoApp->getPrice(
        $premiumInfoThisMonth['activeUsers'],
        $premiumInfoThisMonth['paidApps'],
        $premiumInfoThisMonth['discount']
      );

      if ($discountedPriceForCurrentlyActiveUsers > 0) {
        $mPayment->record->recordCreate([
          'datetime_charged' => date('Y-m-d H:i:s'),
          'full_amount' => -$fullPriceForCurrentlyActiveUsers,
          'discounted_amount' => -$discountedPriceForCurrentlyActiveUsers,
          'discount_percent' => $premiumInfoThisMonth['discount'],
          'notes' => 'currently active ' . $premiumInfoThisMonth['activeUsers'] . ' active users + ' . $premiumInfoThisMonth['paidApps'] . ' paid apps',
          'has_invoice' => true,
        ]);
      }

      // suma za pouzivatelov pridanych minuly mesiac

      $usersAddedPrevMonth = $premiumInfoPrevMonth['activeUsers'] - $premiumInfoPrevPrevMonth['activeUsers'];
      $paidAppsAddedPrevMonth = $premiumInfoPrevMonth['paidApps'] - $premiumInfoPrevPrevMonth['paidApps'];

      $fullPriceForActiveUsersAddedPrevMonth = $this->hubletoApp->getPrice(
        $usersAddedPrevMonth,
        $paidAppsAddedPrevMonth,
        0
      );

      $discountedPriceForActiveUsersAddedPrevMonth = $this->hubletoApp->getPrice(
        $usersAddedPrevMonth,
        $paidAppsAddedPrevMonth,
        $premiumInfoPrevMonth['discount']
      );

      if ($discountedPriceForActiveUsersAddedPrevMonth > 0) {
        $mPayment->record->recordCreate([
          'datetime_charged' => date('Y-m-d H:i:s'),
          'full_amount' => -$fullPriceForActiveUsersAddedPrevMonth,
          'discounted_amount' => -$discountedPriceForActiveUsersAddedPrevMonth,
          'discount_percent' => $premiumInfoPrevMonth['discount'],
          'notes' => 'last month added ' . $premiumInfoPrevMonth['activeUsers'] . ' active users + ' . $premiumInfoPrevMonth['paidApps'] . ' paid apps',
          'has_invoice' => true,
        ]);
      }

      // platba
      $toPay = $discountedPriceForCurrentlyActiveUsers + $discountedPriceForActiveUsersAddedPrevMonth;

      if ($toPay > 0) {
        $mPayment->record->recordCreate([
          'datetime_charged' => date('Y-m-d H:i:s'),
          'full_amount' => $toPay,
          'notes' => 'simulated payment with card'
        ]);
      }

      $this->hubletoApp->recalculateCredit();

      // ak vsetko prebehlo v poriadku a ma nastaveny subscription renewal, predlzim subscription
      if ($subscriptionRenewalActive) {
        $this->hubletoApp->saveConfig('subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
      }

      return [
        'result' => self::PAYMENT_SUCCESS,
        'premiumInfoPrevPrevMonth' => $premiumInfoPrevPrevMonth,
        'premiumInfoPrevMonth' => $premiumInfoPrevMonth,
        'premiumInfoThisMonth' => $premiumInfoThisMonth,
      ];
    }

  }

}