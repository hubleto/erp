<?php

namespace HubletoApp\Community\Cloud;

class Loader extends \HubletoMain\Core\App
{

  public bool $canBeDisabled = false;

  public function __construct(\HubletoMain $main)
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
      '/^cloud\/log\/?$/' => Controllers\Log::class,
      '/^cloud\/test\/make-random-payment\/?$/' => Controllers\Test\MakeRandomPayment::class,
      '/^cloud\/test\/clear-credit\/?$/' => Controllers\Test\ClearCredit::class,
      '/^cloud\/payments\/?$/' => Controllers\Payments::class,
      '/^cloud\/billing-accounts\/?$/' => Controllers\BillingAccounts::class,
      '/^cloud\/upgrade\/?$/' => Controllers\Upgrade::class,
      '/^cloud\/make-payment\/?$/' => Controllers\MakePayment::class,
    ]);

    $this->updatePremiumInfo();

  }

  public function onBeforeRender(): void
  {
    if (!str_starts_with($this->main->route, 'cloud')) {
      if (!$this->configAsBool('legalDocumentsAccepted')) {
        $this->main->router->redirectTo('cloud');
      } else if ($this->main->isPremium) {
        $currentCredit = $this->getCurrentCredit();
        $freeTrialInfo = $this->getFreeTrialInfo();
        if (!$freeTrialInfo['isTrialPeriod'] && $currentCredit <= 0) {
          $this->main->router->redirectTo('cloud');
        }
      }
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\BillingAccount($this->main))->dropTableIfExists()->install();
      (new Models\Log($this->main))->dropTableIfExists()->install();
      (new Models\Credit($this->main))->dropTableIfExists()->install();
      (new Models\Payment($this->main))->dropTableIfExists()->install();
    }
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [

      "HubletoApp/Community/Cloud/Controllers/Cloud",
      "HubletoApp/Community/Cloud/Controllers/Upgrade",

      "HubletoApp/Community/Cloud/Cloud",
    ];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }

  public function getPrice(int $activeUsers, int $paidApps): float
  {
    $pricePerUser = 9.9;
    if ($activeUsers > 1 || $paidApps > 0) {
      return $activeUsers * $pricePerUser;
    } else {
      return 0;
    }
  }

  public function getFreeTrialInfo(): array
  {
    $daysOfFreeTrial = 0;
    $isTrialPeriod = false;
    $trialPeriodExpiresIn = 0;

    $premiumAccountSince = $this->configAsString('premiumAccountSince');

    if (empty($premiumAccountSince)) {
    } else {
      $mPayment = new Models\Payment($this->main);
      $alreadyMadePayment = $mPayment->record->count() > 0;
      if (!$alreadyMadePayment) {
        $daysOfFreeTrial = ceil((time() - strtotime($premiumAccountSince))/3600/24);
        $trialPeriodExpiresIn = 30 - $daysOfFreeTrial;
        $isTrialPeriod = $trialPeriodExpiresIn > 0;
      }
    }

    return [
      'isTrialPeriod' => $isTrialPeriod,
      'daysOfFreeTrial' => $daysOfFreeTrial,
      'trialPeriodExpiresIn' => $trialPeriodExpiresIn,
    ];
  }

  public function dangerouslyInjectDesktopHtmlContent(string $where): string
  {
    $freeTrialInfo = $this->getFreeTrialInfo();
    $isTrialPeriod = $freeTrialInfo['isTrialPeriod'];
    $daysOfFreeTrial = $freeTrialInfo['daysOfFreeTrial'];
    $trialPeriodExpiresIn = $freeTrialInfo['trialPeriodExpiresIn'];

    if ($where == 'beforeSidebar') {
      if ($isTrialPeriod) {
        return '
          <a
            class="badge badge-warning text-center no-underline items-center flex justify-around"
            href="' . $this->main->config->getAsString('accountUrl') . '/cloud?freeTrialMessage=1"
          >
            <span>Free trial activated</span>
          </a>
        ';
      }
    }

    if ($where == 'footer') {
      if ($isTrialPeriod) {
        return '
          <div class="fixed left-0 bottom-0 m-2 shadow-lg" style="z-index:9999">
            <a class="btn btn-warning btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/cloud">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expires in ' .$trialPeriodExpiresIn . ' days. Configure your payment method.</span>
            </a>
          </div>
        ';
      }
    }

    return '';
  }

  // public function generateDemoData(): void
  // {
  //   $mPayment = new Models\Payment($this->main);
  //   $mCredit = new Models\Credit($this->main);

  //   for ($i = 1; $i <= 9; $i++) {
  //     $mPayment->record->recordCreate([
  //       'datetime_charged' => date('Y-m-d ' . rand(13, 16) . ':' . rand(10, 50) . ':' . rand(25, 45), strtotime('-' . rand(3, 30) . ' days')),
  //       'amount' => rand(-100, 100) / rand(3, 7),
  //     ]);
  //   }

  //   $mCredit->record->recordCreate([ 'datetime_recalculated' => date('Y-m-d H:i:s', strtotime('-5 days')), 'credit' => 10.5 ]);

  // }

  public function getPremiumInfo(int $month = 0, int $year = 0): array
  {
    if ($month == 0) $month = date('m');
    if ($year == 0) $year = date('Y');

    $mLog = new \HubletoApp\Community\Cloud\Models\Log($this->main);

    $premiumInfo = $mLog->record
      ->whereMonth('log_datetime', $month)
      ->whereYear('log_datetime', $year)
      ->orderBy('log_datetime', 'desc')
      ->first()
      ?->toArray()
    ;

    if (!is_array($premiumInfo)) {
      $this->updatePremiumInfo();

      $premiumInfo = $mLog->record
        ->whereMonth('log_datetime', $month)
        ->whereYear('log_datetime', $year)
        ->orderBy('price', 'desc')
        ->first()
        ?->toArray()
      ;
    }

    return $premiumInfo;
  }

  public function premiumAccountActivated(): bool
  {
    $activated = !empty($this->configAsString('premiumAccountSince'));
    if (!$activated) {
      $premiumInfo = $this->getPremiumInfo();
      $activated = $premiumInfo['active_users'] > 1 || $premiumInfo['paid_apps'] > 0;

      if ($activated) {
        $this->saveConfig('premiumAccountSince', date('Y-m-d'));
      }
    }

    return $activated;
  }

  public function updatePremiumInfo() {
    $month = (int) date('m');
    $year = (int) date('Y');

    $mLog = new Models\Log($this->main);
    $mPayment = new Models\Payment($this->main);
    $mCredit = new Models\Credit($this->main);

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
      if ($app->manifest['appType'] != \HubletoMain\Core\App::APP_TYPE_COMMUNITY) {
        $paidApps++;
      }
    }

    // count active users
    $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
    $activeUsers = $mUser->record->where('is_active', 1)->count();

    // log change in number of users or paid apps
    if ($activeUsers != $lastKnownActiveUsers || $paidApps != $lastKnownPaidApps) {
      $freeTrialInfo = $this->getFreeTrialInfo();
      $price = ($freeTrialInfo['isTrialPeriod'] ? 0 : $this->getPrice($activeUsers, $paidApps));
      $mLog->record->recordCreate([
        'log_datetime' => date('Y-m-d H:i:s'),
        'active_users' => $activeUsers,
        'paid_apps' => $paidApps,
        'is_premium_expected' => ($activeUsers > 1 || $paidApps > 0),
        'price' => $price,
      ]);
    }
  }

  public function recalculateCredit(): float
  {
    $mPayment = new Models\Payment($this->main);
    $mCredit = new Models\Credit($this->main);

    $lastCreditData = $mCredit->record->orderBy('id', 'desc')->first()?->toArray();
    $lastCreditDatetime = (string) ($lastCreditData['datetime_recalculated'] ?? '');
    $currentCredit = (float) ($lastCreditData['credit'] ?? 0);

    $payments = $mPayment->record;
    if (!empty($lastCreditDatetime)) $payments = $payments->whereRaw('datetime_charged > "' . date('Y-m-d H:i:s', strtotime($lastCreditDatetime)) . '"');

    foreach ($payments->get()?->toArray() as $payment) {
      $currentCredit += (float) ($payment['amount'] ?? 0);
    }

    if ($lastCreditData['credit'] > 0 && $currentCredit <= 0) {
      $this->saveConfig('creditExhaustedOn', date('Y-m-d'));
    }

    $mCredit->record->recordCreate(['datetime_recalculated' => date('Y-m-d H:i:s'), 'credit' => $currentCredit]);

    return (float) $currentCredit;
  }

  public function getCurrentCredit(): float
  {
    $mCredit = new Models\Credit($this->main);
    $tmp = $mCredit->record->orderBy('id', 'desc')->first()?->toArray();

    return (float) ($tmp['credit'] ?? 0);
  }

}