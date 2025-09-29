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
      '/^accounting\/?$/' => \Hubleto\App\Community\Accounting\Controllers\Accounts::class,

      '/^accounting\/receivable\/?$/' => \Hubleto\App\Community\Accounting\Controllers\Receivable::class,
      '/^accounting\/receivable\/add\/?$/' => ['controller' => \Hubleto\App\Community\Accounting\Controllers\Receivable::class, 'vars' => ['recordId' => -1]],

      '/^accounting\/payable\/?$/' => \Hubleto\App\Community\Accounting\Controllers\Payable::class,
      '/^accounting\/payable\/add\/?$/' => ['controller' => \Hubleto\App\Community\Accounting\Controllers\Payable::class, 'vars' => ['recordId' => -1]],

      '/^accounting\/accounts\/add\/?$/' => ['controller' => Controllers\Accounts::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/accounts\/?$/' => Controllers\Accounts::class,
      '/^accounting\/account-types\/add\/?$/' => ['controller' => Controllers\AccountTypes::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/account-types\/?$/' => Controllers\AccountTypes::class,
      '/^accounting\/account-subtypes\/add\/?$/' => ['controller' => Controllers\AccountSubtypes::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/account-subtypes\/?$/' => Controllers\AccountSubtypes::class,

      '/^accounting\/entries\/?$/' => Controllers\Entries::class,
      '/^accounting\/entries\/add\/?$/' => ['controller' => Controllers\Entries::class, 'vars' => ['recordId' => -1]],

      '/^accounting\/transactions?$/' => \Hubleto\App\Community\Accounting\Controllers\Transactions::class,
      '/^accounting\/transactions\/add?$/' => ['controller' => \Hubleto\App\Community\Accounting\Controllers\Transactions::class, 'vars' => ['recordId' => -1]],
      '/^accounting\/api\/get-reconciled-amount?$/' => \Hubleto\App\Community\Accounting\Controllers\Api\GetReconciledAmount::class,
    ]);

    $this->sidebarView = '@Hubleto:App:Community:Accounting/Sidebar.twig';
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Receivable::class)->dropTableIfExists()->install();
      $this->getModel(Models\Payable::class)->dropTableIfExists()->install();
      $this->getModel(Models\AccountType::class)->dropTableIfExists()->install();
      $this->getModel(Models\AccountSubtype::class)->dropTableIfExists()->install();
      $this->getModel(Models\Account::class)->dropTableIfExists()->install();

      $this->getModel(Models\AccountType::class)->record->create(["title" => "Liability"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Asset"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Equity"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Expense"]);
      $this->getModel(Models\AccountType::class)->record->create(["title" => "Revenue"]);

      $this->getModel(Models\Entry::class)->dropTableIfExists()->install();
      $this->getModel(Models\EntryLine::class)->dropTableIfExists()->install();

      $this->getModel(Models\Transaction::class)->dropTableIfExists()->install();
      $this->getModel(Models\Reconciliation::class)->dropTableIfExists()->install();


    }
  }

}