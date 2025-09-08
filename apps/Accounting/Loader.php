<?php

namespace Hubleto\App\Community\Accounting;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^accounting\/?$/' => Controllers\Accounting::class,
      '/^accounting\/accounts\/add\/?$/' => ['controller' => Controllers\Accounts::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/accounts\/?$/' => Controllers\Accounts::class,
      '/^accounting\/account-types\/add\/?$/' => ['controller' => Controllers\AccountTypes::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/account-types\/?$/' => Controllers\AccountTypes::class,
      '/^accounting\/account-subtypes\/add\/?$/' => ['controller' => Controllers\AccountSubtypes::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/account-subtypes\/?$/' => Controllers\AccountSubtypes::class,
//      '/^invoices\/api\/generate-pdf\/?$/' => Controllers\Api\GeneratePdf::class,
//      '/^invoices(\/(?<recordId>\d+))?\/?$/' => Controllers\Invoices::class,
    ]);

    /** @var \Hubleto\App\Community\Pipeline\Manager $pipelineManager */
//    $pipelineManager = $this->getService(\Hubleto\App\Community\Pipeline\Manager::class);
//    $pipelineManager->addPipeline($this, 'invoices', Pipeline::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\AccountType::class)->dropTableIfExists()->install();
      $this->getModel(Models\AccountSubtype::class)->dropTableIfExists()->install();
      $this->getModel(Models\Account::class)->dropTableIfExists()->install();
    } else if ($round == 2) {
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Liability"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Asset"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Equity"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Expense"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Revenue"]);
    }
  }

}