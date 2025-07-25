<?php

namespace HubletoApp\Community\Cloud\Controllers\Api;

class PayMonthly extends \HubletoMain\Controllers\ApiController
{
  public const PAYMENT_SUCCESS = 1;
  public const THIS_IS_NOT_PREMIUM_ACCOUNT = 2;
  public const SUBSCRIPTION_NOT_ACTIVE = 3;
  public const FREE_TRIAL_PERIOD = 4;
  public const THIS_MONTH_ALREADY_PAID = 5;

  public bool $requiresUserAuthentication = false;

  public function renderJson(): ?array
  {
    if ($this->main->isUrlParam('today')) {
      $today = date('Y-m-d', strtotime($this->main->urlParamAsString('today')));
    } else {
      $today = date('Y-m-d');
    }

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


    $mPayment = $this->main->di->create(\HubletoApp\Community\Cloud\Models\Payment::class);

    $prevMonth = date('m', strtotime('-1 month', strtotime($today)));
    $prevYear = date('Y', strtotime('-1 month', strtotime($today)));

    $thisMonth = date('m', strtotime($today));
    $thisYear = date('Y', strtotime($today));

    $paymentPrevMonth = $mPayment->record
      ->where('type', $mPayment::TYPE_SUBSCRIPTION_FEE)
      ->whereMonth('datetime_charged', $prevMonth)
      ->whereYear('datetime_charged', $prevYear)
      ->first()
      ?->toArray()
    ;

    $paymentThisMonth = $mPayment->record
      ->where('type', $mPayment::TYPE_SUBSCRIPTION_FEE)
      ->whereMonth('datetime_charged', $thisMonth)
      ->whereYear('datetime_charged', $thisYear)
      ->first()
      ?->toArray()
    ;

    // var_dump($prevMonth);var_dump($prevYear);
    // var_dump($thisMonth);var_dump($thisYear);
    // var_dump($paymentPrevMonth);var_dump($paymentThisMonth);
    // exit;

    if ($paymentThisMonth !== null) {
      return [
        'result' => self::THIS_MONTH_ALREADY_PAID,
      ];
    } else {

      $paymentThisMonthDetails = @json_decode($paymentThisMonth['details'], true) ?? [];
      $paymentPrevMonthDetails = @json_decode($paymentPrevMonth['details'], true) ?? [];

      $this->hubletoApp->updatePremiumInfo($thisMonth, $thisYear);

      $premiumInfoPrevMonth = $this->hubletoApp->getPremiumInfo($prevMonth, $prevYear);
      $premiumInfoThisMonth = $this->hubletoApp->getPremiumInfo($thisMonth, $thisYear);

      // var_dump($premiumInfoPrevMonth);
      // var_dump($premiumInfoThisMonth);
      // exit;

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
          'datetime_charged' => date('Y-m-d H:i:s', strtotime($today)),
          'full_amount' => -$fullPriceForCurrentlyActiveUsers,
          'discounted_amount' => -$discountedPriceForCurrentlyActiveUsers,
          'discount_percent' => $premiumInfoThisMonth['discount'],
          'details' => '{"activeUsers":' . $premiumInfoThisMonth['activeUsers'] . ',"paidApps":' . $premiumInfoThisMonth['paidApps'] . '}',
          'has_invoice' => true,
          'type' => $mPayment::TYPE_SUBSCRIPTION_FEE,
          'uuid' => \Hubleto\Framework\Helper::generateUuidV4(),
        ]);
      }

      // suma za pouzivatelov pridanych minuly mesiac

      $usersAddedPrevMonth = $premiumInfoPrevMonth['activeUsers'] - ($paymentPrevMonthDetails['activeUsers'] ?? 0);
      $paidAppsAddedPrevMonth = $premiumInfoPrevMonth['paidApps'] - ($paymentPrevMonthDetails['paidApps'] ?? 0);

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
          'datetime_charged' => date('Y-m-d 23:59:59', strtotime('last day of previous month', strtotime($today))),
          'full_amount' => -$fullPriceForActiveUsersAddedPrevMonth,
          'discounted_amount' => -$discountedPriceForActiveUsersAddedPrevMonth,
          'discount_percent' => $premiumInfoPrevMonth['discount'],
          'details' => '{"newActiveUsers":' . $usersAddedPrevMonth . ',"newPaidApps":' . $paidAppsAddedPrevMonth . '}',
          'has_invoice' => true,
          'type' => $mPayment::TYPE_BACK_PAY,
          'uuid' => \Hubleto\Framework\Helper::generateUuidV4(),
        ]);
      }

      // platba
      $toPay = $discountedPriceForCurrentlyActiveUsers + $discountedPriceForActiveUsersAddedPrevMonth;

      if ($toPay > 0) {
        $mPayment->record->recordCreate([
          'datetime_charged' => date('Y-m-d H:i:s', strtotime($today)),
          'full_amount' => $toPay,
          'details' => '{"reason":"simulated payment with card"}',
          'type' => $mPayment::TYPE_PAYMENT_BY_CARD,
          'uuid' => \Hubleto\Framework\Helper::generateUuidV4(),
        ]);
      }

      $this->hubletoApp->recalculateCredit();

      // ak vsetko prebehlo v poriadku a ma nastaveny subscription renewal, predlzim subscription
      if ($subscriptionRenewalActive) {
        $this->hubletoApp->saveConfig('subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
      }

      return [
        'result' => self::PAYMENT_SUCCESS,
        'paymentPrevMonth' => $paymentPrevMonth,
        'paymentThisMonth' => $paymentThisMonth,
        'premiumInfoPrevMonth' => $premiumInfoPrevMonth,
        'premiumInfoThisMonth' => $premiumInfoThisMonth,
      ];
    }

  }

}
