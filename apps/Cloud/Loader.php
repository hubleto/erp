<?php

namespace HubletoApp\Community\Cloud;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->isPremium = $this->premiumAccountActivated();

    $this->main->router->httpGet([
      '/^cloud\/?$/' => Controllers\Dashboard::class,
      '/^cloud\/api\/accept-legal-documents\/?$/' => Controllers\Api\AcceptLegalDocuments::class,
      '/^cloud\/api\/activate-premium-account\/?$/' => Controllers\Api\ActivatePremiumAccount::class,
      '/^cloud\/api\/charge-credit\/?$/' => Controllers\Api\ChargeCredit::class,
      '/^cloud\/api\/pay-monthly\/?$/' => Controllers\Api\PayMonthly::class,
      '/^cloud\/log\/?$/' => Controllers\Log::class,
      '/^cloud\/test\/make-random-payment\/?$/' => Controllers\Test\MakeRandomPayment::class,
      '/^cloud\/test\/clear-credit\/?$/' => Controllers\Test\ClearCredit::class,
      '/^cloud\/activate-subscription-renewal\/?$/' => Controllers\ActivateSubscriptionRenewal::class,
      '/^cloud\/deactivate-subscription-renewal\/?$/' => Controllers\DeactivateSubscriptionRenewal::class,
      '/^cloud\/payments-and-invoices\/?$/' => Controllers\PaymentsAndInvoices::class,
      '/^cloud\/billing-accounts\/?$/' => Controllers\BillingAccounts::class,
      '/^cloud\/upgrade\/?$/' => Controllers\Upgrade::class,
      '/^cloud\/make-payment\/?$/' => Controllers\MakePayment::class,
    ]);

    $this->updatePremiumInfo();

  }

  public function onBeforeRender(): void
  {
    if ($this->main->auth->getUserId() > 0) {
      if (!str_starts_with($this->main->route, 'cloud')) {
        if (!$this->configAsBool('legalDocumentsAccepted')) {
          $this->main->router->redirectTo('cloud');
        } elseif ($this->main->isPremium) {
          $subscriptionInfo = $this->getSubscriptionInfo();
          if (!$subscriptionInfo['isActive']) {
            $this->main->router->redirectTo('cloud');
          }
        }
      }
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->di->create(Models\BillingAccount::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Log::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Credit::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Payment::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\Discount::class)->dropTableIfExists()->install();
    }
  }

  public function getAccountUid()
  {
    $accountUid = $this->configAsString('accountUid');
    if (empty($accountUid)) {
      $accountUid = \Hubleto\Framework\Helper::generateUuidV4();
      $this->saveConfig('accountUid', $accountUid);
    }
    return $accountUid;
  }

  public function getPaymentVariableSymbol()
  {
    $paymentVariableSymbol = $this->configAsString('paymentVariableSymbol');
    if (empty($paymentVariableSymbol)) {
      $paymentVariableSymbol = '3' . date('y') . str_pad(rand(0, 999), 3, STR_PAD_LEFT) . str_pad(rand(0, 9999), 4, STR_PAD_LEFT);
      $this->saveConfig('paymentVariableSymbol', $paymentVariableSymbol);
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

    $premiumAccountSince = $this->configAsString('premiumAccountSince');
    $freeTrialPeriodUntil = $this->configAsString('freeTrialPeriodUntil');
    $isTrialPeriod = $this->configAsBool('isTrialPeriod');

    if (!empty($premiumAccountSince)) {
      $trialPeriodExpiresIn = floor((strtotime($freeTrialPeriodUntil) - time()) / 3600 / 24);
      // $isTrialPeriod = $trialPeriodExpiresIn > 0;
    }

    return [
      'isTrialPeriod' => $isTrialPeriod,
      'trialPeriodExpiresIn' => $trialPeriodExpiresIn,
    ];
  }

  public function getSubscriptionInfo(): array
  {
    $subscriptionRenewalActive = $this->configAsBool('subscriptionRenewalActive');
    $subscriptionActiveUntil = $this->configAsString('subscriptionActiveUntil');
    $subscriptionActive = strtotime($subscriptionActiveUntil) > time();

    return [
      'renewalActive' => $subscriptionRenewalActive,
      'activeUntil' => $subscriptionActiveUntil,
      'isActive' => $subscriptionActive,
    ];
  }

  public function dangerouslyInjectDesktopHtmlContent(string $where): string
  {
    $freeTrialInfo = $this->getFreeTrialInfo();
    $isTrialPeriod = $freeTrialInfo['isTrialPeriod'];
    $trialPeriodExpiresIn = $freeTrialInfo['trialPeriodExpiresIn'];

    // if ($where == 'beforeSidebar') {
    //   if ($isTrialPeriod) {
    //     return '
    //       <a
    //         class="badge badge-warning text-center no-underline items-center flex justify-around"
    //         href="' . $this->main->config->getAsString('rootUrl') . '/cloud?freeTrialMessage=1"
    //       >
    //         <span>Free trial activated</span>
    //       </a>
    //     ';
    //   }
    // }

    if ($where == 'beforeSidebar') {
      if ($isTrialPeriod) {
        return '
          <a class="btn btn-square bg-red-50 text-red-800" href="' . $this->main->config->getAsString('rootUrl') . '/cloud">
            <span class="text">' . $this->translate('Free trial expires in') . ' ' .$trialPeriodExpiresIn . ' ' . $this->translate('days') . '.</span>
          </a>
        ';
      }
    }

    return '';
  }

  public function getPremiumInfo(int $month = 0, int $year = 0): array
  {
    $mDiscount = $this->main->di->create(Models\Discount::class);
    $mLog = $this->main->di->create(Models\Log::class);
    $mPayment = $this->main->di->create(Models\Payment::class);

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
      foreach ($this->main->apps->getEnabledApps() as $app) {
        if ($app->manifest['appType'] != \Hubleto\Framework\App::APP_TYPE_COMMUNITY) {
          $paidApps++;
        }
      }

      // count active users
      $mUser = $this->main->di->create(\HubletoApp\Community\Settings\Models\User::class);
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
    $this->saveConfig('premiumAccountSince', date('Y-m-d H:i:s'));
    $this->saveConfig('subscriptionRenewalActive', '1');
    $this->saveConfig('subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
    $this->saveConfig('isTrialPeriod', '1');
    $this->saveConfig('freeTrialPeriodUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
  }

  public function premiumAccountActivated(): bool
  {
    $activated = !empty($this->configAsString('premiumAccountSince'));
    if (!$activated) {
      $premiumInfo = $this->getPremiumInfo();
      $activated = $premiumInfo['activeUsers'] > 1 || $premiumInfo['paidApps'] > 0;

      if ($activated) {
        $this->activatePremiumAccount();
      }
    }

    return $activated;
  }

  public function updatePremiumInfo(int $month = 0, int $year = 0)
  {
    if ($month == 0) {
      $month = (int) date('m');
    }
    if ($year == 0) {
      $year = (int) date('Y');
    }

    $mLog = $this->main->di->create(Models\Log::class);
    $mPayment = $this->main->di->create(Models\Payment::class);
    $mCredit = $this->main->di->create(Models\Credit::class);

    $lastLog = $mLog->record
      ->orderBy('log_datetime', 'desc')
      ->first()
      ?->toArray()
    ;

    $lastKnownActiveUsers = (int) ($lastLog['active_users'] ?? 0);
    $lastKnownPaidApps = (int) ($lastLog['paid_apps'] ?? 0);

    // count enabled non-community apps
    $paidApps = 0;
    foreach ($this->main->apps->getEnabledApps() as $app) {
      if ($app->manifest['appType'] != \Hubleto\Framework\App::APP_TYPE_COMMUNITY) {
        $paidApps++;
      }
    }

    // count active users
    $mUser = $this->main->di->create(\HubletoApp\Community\Settings\Models\User::class);
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
    $mPayment = $this->main->di->create(Models\Payment::class);
    $mCredit = $this->main->di->create(Models\Credit::class);

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
      $this->saveConfig('creditExhaustedOn', date('Y-m-d'));
    }

    $mCredit->record->recordCreate(['datetime_recalculated' => date('Y-m-d H:i:s'), 'credit' => $currentCredit]);

    return (float) $currentCredit;
  }

  public function getCurrentCredit(): float
  {
    $mCredit = $this->main->di->create(Models\Credit::class);
    $tmp = $mCredit->record->orderBy('id', 'desc')->first()?->toArray();

    return (float) ($tmp['credit'] ?? 0);
  }

  public function generateDemoData(): void
  {
    $this->saveConfig('premiumAccountSince', date('Y-m-d H:i:s'));
    $this->saveConfig('subscriptionRenewalActive', '1');
    $this->saveConfig('subscriptionActiveUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
    $this->saveConfig('freeTrialPeriodUntil', date('Y-m-d H:i:s', strtotime('+1 month')));
  }
}
