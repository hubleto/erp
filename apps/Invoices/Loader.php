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
      '/^invoices\/api\/get-preview-html\/?$/' => Controllers\Api\GetPreviewHtml::class,
      '/^invoices\/api\/get-preview-vars\/?$/' => Controllers\Api\GetPreviewVars::class,
      '/^invoices\/api\/link-prepared-item\/?$/' => Controllers\Api\LinkPreparedItem::class,
      '/^invoices\/api\/unlink-prepared-item\/?$/' => Controllers\Api\UnlinkPreparedItem::class,
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

  /**
   * [Description for installTables]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Payment::class)->dropTableIfExists()->install();
      $this->getModel(Models\Invoice::class)->dropTableIfExists()->install();
      $this->getModel(Models\Profile::class)->dropTableIfExists()->install();
      $this->getModel(Models\Item::class)->dropTableIfExists()->install();
    }
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices">
          <span class="icon"><i class="fas fa-file-invoice"></i></span>
          <span class="text">' . $this->translate('All invoices') . '</span>
        </a>
        <a class="btn btn-transparent btn-small ml-4" href="' . $this->env()->projectUrl . '/invoices?view=outboundInvoices">
          <span class="icon"><i class="fas fa-cart-shopping"></i></span>
          <span class="text">' . $this->translate('Outbound invoices') . '</span>
        </a>
        <a class="btn btn-transparent btn-small ml-4" href="' . $this->env()->projectUrl . '/invoices?view=inboundInvoices">
          <span class="icon"><i class="fas fa-euro-sign"></i></span>
          <span class="text">' . $this->translate('Inbound invoices') . '</span>
        </a>
        <br/>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/items?filters[fStatus]=1">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Prepared items') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/payments">
          <span class="icon"><i class="fas fa-euro-sign"></i></span>
          <span class="text">' . $this->translate('Payments') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/profiles">
          <span class="icon"><i class="fas fa-address-card"></i></span>
          <span class="text">' . $this->translate('Profiles') . '</span>
        </a>
      </div>
    ';
  }

}