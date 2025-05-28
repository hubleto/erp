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

    $this->main->isPremium = $this->shouldHavePremium();

    $this->main->router->httpGet([
      '/^cloud\/?$/' => Controllers\Dashboard::class,
      '/^cloud\/accept-legal-documents\/?$/' => Controllers\AcceptLegalDocuments::class,
      '/^cloud\/log\/?$/' => Controllers\Log::class,
      '/^cloud\/test\/make-random-payment\/?$/' => Controllers\Test\MakeRandomPayment::class,
      '/^cloud\/test\/clear-credit\/?$/' => Controllers\Test\ClearCredit::class,
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

  public function getFreeTrialInfo(): array
  {
    $daysOfFreeTrial = 0;
    $isTrialPeriod = false;
    $trialPeriodExpiresIn = 0;

    $mLog = new Models\Log($this->main);
    $shouldHavePremiumFrom = $mLog->record
      ->where('premium_expected', true)
      ->first()
      ?->toArray()
    ;

    if ($shouldHavePremiumFrom !== null) {
      $mPayment = new Models\Payment($this->main);
      $alreadyMadePayment = $mPayment->record->count() > 0;
      if (!$alreadyMadePayment) {
        $date = $shouldHavePremiumFrom['date'];
        $daysOfFreeTrial = ceil((time() - strtotime($date))/3600/24);
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
            class="badge badge-info text-center no-underline items-center flex justify-around"
            href="' . $this->main->config->getAsString('accountUrl') . '/cloud?freeTrialMessage=1"
          >
            <i class="fas fa-warning"></i>
            <span>Free trial activated</span>
          </a>
        ';
      }
    }

    if ($where == 'footer') {
      if ($isTrialPeriod) {
        if ($daysOfFreeTrial <= 20) {
          $freeTrialMessageHtml = '
            <a class="btn btn-info btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/cloud/configure-payment">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expires in ' .$trialPeriodExpiresIn . ' days. Configure your payment method.</span>
            </a>
          ';
        } else if ($daysOfFreeTrial <= 25) {
          $freeTrialMessageHtml = '
            <a class="btn btn-warning btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/cloud/configure-payment">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expires in ' .$trialPeriodExpiresIn . ' days. Configure your payment method.</span>
            </a>
          ';
        } else if ($daysOfFreeTrial <= 30) {
          $freeTrialMessageHtml = '
            <a class="btn btn-danger btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/cloud/configure-payment">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expires in ' . $trialPeriodExpiresIn . ' days. Configure your payment method.</span>
            </div>
          ';
        } else {
          $freeTrialMessageHtml = '
            <a class="btn btn-danger btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/cloud/configure-payment">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expired. Configure your payment method.</span>
            </a>
          ';
        }

        return '
          <div class="fixed left-0 bottom-0 m-2" style="z-index:9999">
            ' . $freeTrialMessageHtml . '
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

  public function getPremiumInfo(): array
  {
    $mLog = new \HubletoApp\Community\Cloud\Models\Log($this->main);

    $premiumInfo = $mLog->record
      ->selectRaw('
        max(ifnull(active_users, 0)) as max_active_users,
        max(ifnull(paid_apps, 0)) as max_paid_apps
      ')
      ->whereMonth('date', date('m'))
      ->whereYear('date', date('Y'))
      ->first()
      ?->toArray()
    ;

    if (!is_array($premiumInfo)) {
      $this->updatePremiumInfo();

      $premiumInfo = $mLog->record
        ->selectRaw('
          max(ifnull(active_users, 0)) as max_active_users,
          max(ifnull(paid_apps, 0)) as max_paid_apps
        ')
        ->whereMonth('date', date('m'))
        ->whereYear('date', date('Y'))
        ->first()
        ?->toArray()
      ;
    }

    return $premiumInfo;
  }

  public function shouldHavePremium(): bool
  {
    $premiumInfo = $this->getPremiumInfo();
    return $premiumInfo['max_active_users'] > 1 || $premiumInfo['max_paid_apps'] > 1;
  }

  public function updatePremiumInfo() {
    $month = (int) date('m');
    $year = (int) date('Y');

    $mLog = new Models\Log($this->main);
    $mPayment = new Models\Payment($this->main);
    $mCredit = new Models\Credit($this->main);

    $lastLog = $mLog->record
      ->orderBy('date', 'desc')
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
      $mLog->record->recordCreate([
        'date' => date('Y-m-d'),
        'active_users' => $activeUsers,
        'paid_apps' => $paidApps,
        'premium_expected' => ($activeUsers > 1 || $paidApps > 0),
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