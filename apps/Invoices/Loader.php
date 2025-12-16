<?php

namespace Hubleto\App\Community\Invoices;

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
      '/^invoices\/api\/generate-pdf\/?$/' => Controllers\Api\GeneratePdf::class,
      '/^invoices(\/(?<recordId>\d+))?\/?$/' => Controllers\Invoices::class,
      '/^invoices\/add?\/?$/' => ['controller' => Controllers\Invoices::class, 'vars' => [ 'recordId' => -1 ]],
      '/^invoices\/profiles(\/(?<recordId>\d+))?\/?$/' => Controllers\Profiles::class,
      '/^invoices\/profiles\/add?\/?$/' => ['controller' => Controllers\Profiles::class, 'vars' => [ 'recordId' => -1 ]],
      '/^invoices\/payments(\/(?<recordId>\d+))?\/?$/' => Controllers\Payments::class,
      '/^invoices\/payments\/add?\/?$/' => ['controller' => Controllers\Payments::class, 'vars' => [ 'recordId' => -1 ]],
      '/^invoices\/items(\/(?<recordId>\d+))?\/?$/' => Controllers\Items::class,
      '/^invoices\/items\/add?\/?$/' => ['controller' => Controllers\Items::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflow($this, 'invoices', Workflow::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Invoice::class)->dropTableIfExists()->install();
      $this->getModel(Models\Profile::class)->dropTableIfExists()->install();
      $this->getModel(Models\Item::class)->dropTableIfExists()->install();
      $this->getModel(Models\InvoiceDocument::class)->dropTableIfExists()->install();
    }
  }

}