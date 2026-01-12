<?php

namespace Hubleto\App\Community\Cloud\Controllers\Api;

use Hubleto\App\Community\Cloud\PremiumAccount;

class PayMonthly extends \Hubleto\Erp\Controllers\ApiController
{
  public const PAYMENT_SUCCESS = 1;
  public const THIS_IS_NOT_PREMIUM_ACCOUNT = 2;
  public const SUBSCRIPTION_NOT_ACTIVE = 3;
  public const FREE_TRIAL_PERIOD = 4;
  public const THIS_MONTH_ALREADY_PAID = 5;

  public bool $requiresAuthenticatedUser = false;

  public function renderJson(): array
  {
    $premiumAccount = $this->getService(PremiumAccount::class);

    if ($this->router()->isUrlParam('today')) {
      $today = date('Y-m-d', strtotime($this->router()->urlParamAsString('today')));
    } else {
      $today = date('Y-m-d');
    }

    if (!$premiumAccount->premiumAccountActivated() > 0) {
      return [ 'result' => self::THIS_IS_NOT_PREMIUM_ACCOUNT ];
    }

    $subscriptionInfo = $premiumAccount->getSubscriptionInfo();
    $subscriptionRenewalActive = $subscriptionInfo['renewalActive'];
    $subscriptionActiveUntil = $subscriptionInfo['activeUntil'];
    $subscriptionActive = strtotime($subscriptionActiveUntil) > time();

    if (!$subscriptionActive && !$subscriptionRenewalActive) {
      return [ 'result' => self::SUBSCRIPTION_NOT_ACTIVE ];
    }

    $freeTrialInfo = $premiumAccount->getFreeTrialInfo();
    $freeTrialPeriodUntil = $freeTrialInfo['trialPeriodUntil'];

    if (strtotime($freeTrialPeriodUntil) > time()) {
      return [ 'result' => self::FREE_TRIAL_PERIOD ];
    }

    /** @var \Hubleto\App\Community\Cloud\Models\Payment */
    $mPayment = $this->getModel(\Hubleto\App\Community\Cloud\Models\Payment::class);

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

      $premiumAccount->updatePremiumInfo($thisMonth, $thisYear);

      $premiumInfoPrevMonth = $premiumAccount->getPremiumInfo($prevMonth, $prevYear);
      $premiumInfoThisMonth = $premiumAccount->getPremiumInfo($thisMonth, $thisYear);

      // var_dump($premiumInfoPrevMonth);
      // var_dump($premiumInfoThisMonth);
      // exit;

      // suma za pouzivatelov tento mesiac

      $fullPriceForCurrentlyActiveUsers = $premiumAccount->getPrice(
        $premiumInfoThisMonth['activeUsers'],
        $premiumInfoThisMonth['paidApps'],
        0
      );

      $discountedPriceForCurrentlyActiveUsers = $premiumAccount->getPrice(
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

      $fullPriceForActiveUsersAddedPrevMonth = $premiumAccount->getPrice(
        $usersAddedPrevMonth,
        $paidAppsAddedPrevMonth,
        0
      );

      $discountedPriceForActiveUsersAddedPrevMonth = $premiumAccount->getPrice(
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
        $mPayment->makePayment(
          $today,
          $toPay,
          '{"reason":"simulated payment with card"}',
          $mPayment::TYPE_PAYMENT_BY_CARD
        );
      }

      $premiumAccount->recalculateCredit();

      // ak vsetko prebehlo v poriadku a ma nastaveny subscription renewal, predlzim subscription
      if ($subscriptionRenewalActive) {
        $premiumAccount->extendSubscriptionByOneMonth();
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
