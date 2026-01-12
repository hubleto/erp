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
      '/^invoices\/api\/create-invoice-from-prepared-item\/?$/' => Controllers\Api\CreateInvoiceFromPreparedItem::class,
      '/^invoices\/api\/link-prepared-item\/?$/' => Controllers\Api\LinkPreparedItem::class,
      '/^invoices\/api\/unlink-prepared-item\/?$/' => Controllers\Api\UnlinkPreparedItem::class,
      '/^invoices\/api\/send-invoice-in-email\/?$/' => Controllers\Api\SendInvoiceInEmail::class,
      '/^invoices(\/(?<recordId>\d+))?\/?$/' => Controllers\Invoices::class,
      '/^invoices\/add?\/?$/' => ['controller' => Controllers\Invoices::class, 'vars' => [ 'recordId' => -1 ]],
      '/^invoices\/profiles(\/(?<recordId>\d+))?\/?$/' => Controllers\Profiles::class,
      '/^invoices\/profiles\/add?\/?$/' => ['controller' => Controllers\Profiles::class, 'vars' => [ 'recordId' => -1 ]],
      '/^invoices\/payments(\/(?<recordId>\d+))?\/?$/' => Controllers\Payments::class,
      '/^invoices\/payments\/add?\/?$/' => ['controller' => Controllers\Payments::class, 'vars' => [ 'recordId' => -1 ]],
      '/^invoices\/payment-methods(\/(?<recordId>\d+))?\/?$/' => Controllers\PaymentMethods::class,
      '/^invoices\/payment-methods\/add?\/?$/' => ['controller' => Controllers\PaymentMethods::class, 'vars' => [ 'recordId' => -1 ]],
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
      $this->getModel(Models\PaymentMethod::class)->dropTableIfExists()->install();
      $this->getModel(Models\Invoice::class)->dropTableIfExists()->install();
      $this->getModel(Models\Profile::class)->dropTableIfExists()->install();
      $this->getModel(Models\Item::class)->dropTableIfExists()->install();
    }
  }

  /**
   * [Description for getSidebarBadgeNumber]
   *
   * @return int
   * 
   */
  public function getSidebarBadgeNumber(): int
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);

    return
      $counter->preparedItems()
      + $counter->dueInvoices()
      + $counter->unsentInvoices()
    ;
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    /** @var Counter */
    $counter = $this->getService(Counter::class);

    $preparedItemsCount = $counter->preparedItems();
    $dueInvoicesCount = $counter->dueInvoices();
    $unsentInvoicesCount = $counter->unsentInvoices();

    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices">
          <span class="icon"><i class="fas fa-file-invoice"></i></span>
          <span class="text">' . $this->translate('Invoices') . '</span>
        </a>

        ' . ($dueInvoicesCount > 0 ? '<div class="badge badge-danger text-xs">Due: ' . $dueInvoicesCount . '</div>' : '') . '
        ' . ($unsentInvoicesCount > 0 ? '<div class="badge badge-danger text-xs">Not sent: ' . $unsentInvoicesCount . '</div>' : '') . '

        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/items">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Items') . '</span>
        ' . ($preparedItemsCount > 0 ? '<span class="badge badge-danger ml-auto">' . $preparedItemsCount . '</span>' : '') . '
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/payments">
          <span class="icon"><i class="fas fa-euro-sign"></i></span>
          <span class="text">' . $this->translate('Payments') . '</span>
        </a>
        <div class="mt-4">
          <b>Settings</b>
          <div class="btn-group vertical mt-2 w-full">
            <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/payment-methods">
              <span class="icon"><i class="fas fa-wallet"></i></span>
              <span class="text">' . $this->translate('Payment methods') . '</span>
            </a>
            <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/profiles">
              <span class="icon"><i class="fas fa-address-card"></i></span>
              <span class="text">' . $this->translate('Invoicing profiles') . '</span>
            </a>
          </div>
        </div>
      </div>
    ';
  }

}