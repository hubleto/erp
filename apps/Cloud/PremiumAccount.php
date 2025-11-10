<?php

namespace Hubleto\App\Community\Cloud;

class PremiumAccount extends \Hubleto\Framework\Core
{

  public function getAccountUid()
  {
    $accountUid = $this->config()->getAsString('cloud/accountUid');
    if (empty($accountUid)) {
      $accountUid = \Hubleto\Framework\Helper::generateUuidV4();
      $this->config()->save('cloud/accountUid', $accountUid);
    }
    return $accountUid;
  }

  public function getPaymentVariableSymbol()
  {
    $paymentVariableSymbol = $this->config()->getAsString('cloud/paymentVariableSymbol');
    if (empty($paymentVariableSymbol)) {
      $paymentVariableSymbol = '3' . date('y') . str_pad(rand(0, 999), 3, STR_PAD_LEFT) . str_pad(rand(0, 9999), 4, STR_PAD_LEFT);
      $this->config()->save('cloud/paymentVariableSymbol', $paymentVariableSymbol);
    }
    return $paymentVariableSymbol;
  }

  public function getPrice(int $activeUsers, int $paidApps, int $discountPercent): float
  {
    $pricePerUser = 9.9;
    if ($this->premiumAccountActivated()) {
      if ($discountPercent > 100) {
        $discountPercent = 0;
      }
      return $activeUsers * $pricePerUser * (100 - $discountPercent) / 100;
    } else {
      return 0;
    }
  }

  public function getFreeTrialInfo(): array
  {
    $trialPeriodExpiresIn = 0;

    $premiumAccountSince = $this->config()->getAsString('cloud/premiumAccountSince');
    $freeTrialPeriodUntil = $this->config()->getAsString('cloud/freeTrialPeriodUntil');
    $isTrialPeriod = $this->config()->getAsBool('cloud/isTrialPeriod');

    if (!empty($premiumAccountSince)) {
      $trialPeriodExpiresIn = floor((strtotime($freeTrialPeriodUntil) - time()) / 3600 / 24);
    }

    return [
      'isTrialPeriod' => $isTrialPeriod,
      'isTrialPeriodExpired' => $isTrialPeriod && ($trialPeriodExpiresIn <= 0),
      'trialPeriodExpiresIn' => $trialPeriodExpiresIn,
      'trialPeriodUntil' => $freeTrialPeriodUntil,
    ];
  }

  public function getSubscriptionInfo(): array
  {
    $subscriptionRenewalActive = $this->config()->getAsBool('cloud/subscriptionRenewalActive');
    $subscriptionActiveUntil = $this->config()->getAsString('cloud/subscriptionActiveUntil');
    $subscriptionActive = strtotime($subscriptionActiveUntil) > time();

    return [
      'renewalActive' => $subscriptionRenewalActive,
      'activeUntil' => $subscriptionActiveUntil,
      'isActive' => $subscriptionActive,
    ];
  }
  
  public function updatePremiumInfo(int $month = 0, int $year = 0)
  {
    if ($month == 0) {
      $month = (int) date('m');
    }
    if ($year == 0) {
      $year = (int) date('Y');
    }

    $mLog = $this->getModel(Models\Log::class);

    $lastLog = $mLog->record
      ->orderBy('log_datetime', 'desc')
      ->first()
      ?->toArray()
    ;

    $lastKnownActiveUsers = (int) ($lastLog['active_users'] ?? 0);
    $lastKnownPaidApps = (int) ($lastLog['paid_apps'] ?? 0);

    // count enabled non-community apps
    $paidApps = 0;
    foreach ($this->appManager()->getEnabledApps() as $app) {
      if ($app->manifest['appType'] != \Hubleto\Framework\App::APP_TYPE_COMMUNITY) {
        $paidApps++;
      }
    }

    // count active users
    $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
    $activeUsers = $mUser->record->where('is_active', 1)->count();

    // log change in number of users or paid apps
    if ($activeUsers != $lastKnownActiveUsers || $paidApps != $lastKnownPaidApps) {
      $freeTrialInfo = $this->getFreeTrialInfo();
      $mLog->record->recordCreate([
        'log_datetime' => date('Y-m-d H:i:s'),
        'active_users' => $activeUsers,
        'paid_apps' => $paidApps,
        'is_premium_expected' => ($activeUsers > 1 || $paidApps > 0),
      ]);
    }
  }

  public function recalculateCredit(): float
  {
    $mPayment = $this->getModel(Models\Payment::class);
    $mCredit = $this->getModel(Models\Credit::class);

    $lastCreditData = $mCredit->record->orderBy('id', 'desc')->first()?->toArray();
    $currentCredit = 0;

    $payments = $mPayment->record;

    foreach ($payments->get()?->toArray() as $payment) {
      $fullAmount = (float) ($payment['full_amount'] ?? 0);
      $discountedAmount = (float) ($payment['discounted_amount'] ?? 0);

      if ($fullAmount > 0) {
        // ide o navysenie kreditu, nepouzivam zlavu
        $currentCredit += $fullAmount;
      } else {
        // ide o platbu za pouzitie, pouzivam zlavu
        $currentCredit += $discountedAmount;
      }
    }

    if (is_array($lastCreditData) && $lastCreditData['credit'] > 0 && $currentCredit <= 0) {
      $this->config()->save('cloud/creditExhaustedOn', date('Y-m-d'));
    }

    $mCredit->record->recordCreate(['datetime_recalculated' => date('Y-m-d H:i:s'), 'credit' => $currentCredit]);

    return (float) $currentCredit;
  }

  public function getCurrentCredit(): float
  {
    $mCredit = $this->getModel(Models\Credit::class);
    $tmp = $mCredit->record->orderBy('id', 'desc')->first()?->toArray();

    return (float) ($tmp['credit'] ?? 0);
  }

  public function getPremiumInfo(int $month = 0, int $year = 0): array
  {
    $mDiscount = $this->getModel(Models\Discount::class);
    $mLog = $this->getModel(Models\Log::class);
    $mPayment = $this->getModel(Models\Payment::class);

    if ($month == 0) {
      $month = date('m');
    }
    if ($year == 0) {
      $year = date('Y');
    }

    $premiumInfo = [
      'activeUsers' => 0,
      'paidApps' => 0,
      'discount' => 0,
      'paymentBase' => 0,
      'paymentWithDiscount' => 0,
    ];


    $log = $mLog->record
      ->selectRaw('
        max(ifnull(active_users, 0)) as max_active_users,
        max(ifnull(paid_apps, 0)) as max_paid_apps
      ')
      ->whereMonth('log_datetime', $month)
      ->whereYear('log_datetime', $year)
      ->first()
      ?->toArray()
    ;

    if ($log['max_active_users'] === null || $log['max_paid_apps'] === null) {
      // count enabled non-community apps
      $paidApps = 0;
      foreach ($this->appManager()->getEnabledApps() as $app) {
        if ($app->manifest['appType'] != \Hubleto\Framework\App::APP_TYPE_COMMUNITY) {
          $paidApps++;
        }
      }

      // count active users
      $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
      $activeUsers = $mUser->record->where('is_active', 1)->count();

      $premiumInfo['activeUsers'] = $activeUsers;
      $premiumInfo['paidApps'] = $paidApps;
    } else {
      $premiumInfo['activeUsers'] = $log['max_active_users'] ?? 0;
      $premiumInfo['paidApps'] = $log['max_paid_apps'] ?? 0;
    }

    $discount = $mDiscount->record
      ->where('month', $month)
      ->where('year', $year)
      ->first()
      ?->toArray()
    ;

    if (is_array($discount)) {
      $premiumInfo['discount'] = $discount['discount_percent'] ?? 0;
    }

    $payment = $mPayment->record
      ->selectRaw('
        sum(ifnull(full_amount, 0)) as full_amount_total,
        sum(ifnull(discounted_amount, 0)) as discounted_amount_total
      ')
      ->whereMonth('datetime_charged', $month)
      ->whereYear('datetime_charged', $year)
      ->where('full_amount', '<', 0)
      ->first()
      ?->toArray()
    ;

    if (is_array($discount)) {
      $premiumInfo['paymentBase'] = $discount['full_amount_total'] ?? 0;
      $premiumInfo['paymentWithDiscount'] = $discount['discounted_amount_total'] ?? 0;
    }

    return $premiumInfo;

  }

  public function activatePremiumAccount(): void
  {
    $this->config()->save('cloud/premiumAccountSince', date('Y-m-d H:i:s'));
    $this->config()->save('cloud/isTrialPeriod', '1');
    $this->config()->save('cloud/freeTrialPeriodUntil', date('Y-m-d H:i:s', strtotime('+1 month')));

    $this->activateSubscriptionRenewal();
  }

  public function activateSubscriptionRenewal(): void
  {
    $this->config()->save('cloud/subscriptionRenewalActive', '1');
    $this->extendSubscriptionByOneMonth();
  }

  public function deactivateSubscriptionRenewal(): void
  {
    $this->config()->save('cloud/subscriptionRenewalActive', '0');
  }

  public function extendSubscriptionByOneMonth()
  {
    $this->config()->save('cloud/subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
  }

  public function premiumAccountActivated(): bool
  {
    $activated = !empty($this->config()->getAsString('cloud/premiumAccountSince'));
    if (!$activated) {
      $premiumInfo = $this->getPremiumInfo();
      $activated = $premiumInfo['activeUsers'] > 1 || $premiumInfo['paidApps'] > 0;

      if ($activated) {
        $this->activatePremiumAccount();
      }
    }

    return $activated;
  }
}