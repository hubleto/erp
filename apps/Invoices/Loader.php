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
    ]);

    /** @var \Hubleto\App\Community\Pipeline\Manager $pipelineManager */
    $pipelineManager = $this->getService(\Hubleto\App\Community\Pipeline\Manager::class);
    $pipelineManager->addPipeline($this, 'invoices', Pipeline::class);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Invoice::class)->dropTableIfExists()->install();
      $this->getModel(Models\InvoiceItem::class)->dropTableIfExists()->install();
      $this->getModel(Models\InvoiceDocument::class)->dropTableIfExists()->install();
    }
  }

}