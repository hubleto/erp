<?php

namespace HubletoApp\Community\Billing;

use HubletoApp\Community\Settings\Models\Permission;

class Loader extends \Hubleto\Framework\App
{
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^billing\/?$/' => Controllers\BillingAccounts::class,
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->main->di->create(Models\BillingAccount::class)->dropTableIfExists()->install();
      $this->main->di->create(Models\BillingAccountService::class)->dropTableIfExists()->install();
    }
  }

}
