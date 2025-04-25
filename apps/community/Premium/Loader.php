<?php

namespace HubletoApp\Community\Premium;

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

    $this->main->router->httpGet([
      '/^premium\/?$/' => Controllers\Premium::class,
      '/^premium\/payment\/?$/' => Controllers\Payment::class,
      '/^premium\/upgrade\/?$/' => Controllers\Upgrade::class,
      '/^premium\/you-are-upgraded\/?$/' => Controllers\PremiumActivated::class,
    ]);

    $lastCheckDate = $this->configAsString('lastCheckDate');

    if ($lastCheckDate != date('Y-m-d')) {
      $month = (int) date('m');
      $year = (int) date('Y');

      $mLog = new Models\Log($this->main);
      $mPayment = new Models\Payment($this->main);
      $mCredit = new Models\Credit($this->main);

      $thisMonthLog = $mLog->record
        ->selectRaw('
          ifnull(max(active_users), 0) as active_users,
          ifnull(max(paid_apps), 0) as paid_apps
        ')
        ->whereYear('date', $year)
        ->whereMonth('date', $month)
        ->first()
        ?->toArray()
      ;

      $thisMonthActiveUsers = (int) $thisMonthLog['active_users'];
      $thisMonthPaidApps = (int) $thisMonthLog['paid_apps'];

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

      if ($activeUsers > $thisMonthActiveUsers || $paidApps > $thisMonthPaidApps) {
        $mLog->record->recordCreate([
          'date' => date('Y-m-d'),
          'active_users' => $activeUsers,
          'paid_apps' => $paidApps,
          'premium_expected' => ($activeUsers > 1 || $paidApps > 0),
        ]);
      }

      // save current status
      $this->saveConfig('lastCheckDate', date('Y-m-d'));
      $this->setConfigAsString('lastCheckDate', date('Y-m-d'));

      // calculate credit
      $this->recalculateCredit();
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

      "HubletoApp/Community/Premium/Controllers/Premium",
      "HubletoApp/Community/Premium/Controllers/Upgrade",
      "HubletoApp/Community/Premium/Controllers/PremiumActivated",

      "HubletoApp/Community/Premium/Premium",
      "HubletoApp/Community/Premium/PremiumActivated",
    ];

    foreach ($permissions as $permission) {
      $mPermission->record->recordCreate([
        "permission" => $permission
      ]);
    }
  }

  public function dangerouslyInjectDesktopHtmlContent(string $where): string
  {
    $daysOfFreeTrial = 0;
    $shouldHavePremium = false;

    if (!$this->main->isPremium) {
      $mLog = new Models\Log($this->main);
      $shouldHavePremiumFrom = $mLog->record
        ->where('premium_expected', true)
        ->first()
        ?->toArray()
      ;

      if ($shouldHavePremiumFrom !== null) {
        $date = $shouldHavePremiumFrom['date'];
        $shouldHavePremium = true;
        $daysOfFreeTrial = ceil((time() - strtotime($date))/3600/24);
      }
    }

    if ($where == 'beforeSidebar') {
      if ($shouldHavePremium && !$this->main->isPremium) {
        return '
          <a
            class="badge badge-info text-center no-underline items-center flex justify-around"
            href="' . $this->main->config->getAsString('accountUrl') . '/premium?freeTrialMessage=1"
          >
            <i class="fas fa-warning"></i>
            <span>Free trial activated</span>
          </a>
        ';
      }
    }

    if ($where == 'footer') {
      if ($shouldHavePremium && !$this->main->isPremium) {
        if ($daysOfFreeTrial < 20) {
          $freeTrialMessageHtml = '
            <a class="btn btn-info btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/premium?freeTrialMessage=1">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial activated for ' . $daysOfFreeTrial . ' days. Configure your payment method.</span>
            </a>
          ';
        } else if ($daysOfFreeTrial < 25) {
          $freeTrialMessageHtml = '
            <a class="btn btn-warning btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/premium?freeTrialMessage=2">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expires in ' . (30 - $daysOfFreeTrial) . ' days. Configure your payment method.</span>
            </a>
          ';
        } else if ($daysOfFreeTrial < 30) {
          $freeTrialMessageHtml = '
            <a class="btn btn-danger btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/premium?freeTrialMessage=3">
              <span class="icon"><i class="fas fa-warning"></i></span>
              <span class="text">Free trial expires in ' . (30 - $daysOfFreeTrial) . ' days. Configure your payment method.</span>
            </div>
          ';
        } else {
          $freeTrialMessageHtml = '
            <a class="btn btn-danger btn-large" href="' . $this->main->config->getAsString('accountUrl') . '/premium?freeTrialMessage=4">
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

  public function generateDemoData(): void
  {
    $mPayment = new Models\Payment($this->main);
    $mCredit = new Models\Credit($this->main);

    for ($i = 1; $i <= 9; $i++) {
      $mPayment->record->recordCreate([
        'datetime_charged' => date('Y-m-d ' . rand(13, 16) . ':' . rand(10, 50) . ':' . rand(25, 45), strtotime('-' . rand(3, 30) . ' days')),
        'amount' => rand(-100, 100) / rand(3, 7),
      ]);
    }

    $mCredit->record->recordCreate([ 'datetime_recalculated' => date('Y-m-d H:i:s', strtotime('-5 days')), 'credit' => 10.5 ]);

  }

  public function recalculateCredit(): float
  {
    $mPayment = new Models\Payment($this->main);
    $mCredit = new Models\Credit($this->main);

    $lastCreditData = $mCredit->record->orderBy('id', 'desc')->first()?->toArray();
    $lastCreditDatetime = (string) ($lastCreditData['datetime_recalculated'] ?? '');
    $currentCredit = (float) ($lastCreditData['credit'] ?? 0);
// var_dump($currentCredit);

    $payments = $mPayment->record;
    if (!empty($lastCreditDatetime)) $payments = $payments->whereRaw('datetime_charged > "' . date('Y-m-d H:i:s', strtotime($lastCreditDatetime)) . '"');

    foreach ($payments->get()?->toArray() as $payment) {
      $currentCredit += (float) ($payment['amount'] ?? 0);
    }

//     var_dump($lastCreditDatetime);
// var_dump($payments->get()?->toArray());
// var_dump($currentCredit);
// exit;

    $mCredit->record->recordCreate(['datetime_recalculated' => date('Y-m-d H:i:s'), 'credit' => $currentCredit]);

    return (float) $currentCredit;
  }

}