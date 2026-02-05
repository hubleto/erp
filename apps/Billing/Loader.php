<?php

namespace Hubleto\App\Community\Billing;

use Hubleto\App\Community\Settings\Models\Permission;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\BillingAccount::class)->dropTableIfExists()->install();
    }
  }

}
