<?php

namespace Hubleto\App\Community\Invoices;

class Loader extends \Hubleto\Erp\App
{

  private int $preparedItemsCount = 0;
  private int $notPaidInvoicesCount = 0;
  private int $dueAndNotPaidInvoicesCount = 0;
  private int $unsentInvoicesCount = 0;

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
      // '/^invoices\/api\/generate-pdf\/?$/' => Controllers\Api\GeneratePdf::class,
      // '/^invoices\/api\/get-preview-html\/?$/' => Controllers\Api\GetPreviewHtml::class,
      // '/^invoices\/api\/get-preview-vars\/?$/' => Controllers\Api\GetPreviewVars::class,
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
      '/^invoices\/api\/create-invoice-from-order?\/?$/' => Controllers\Api\CreateInvoiceFromOrder::class,
    ]);

    $this->addSearchSwitch('i', 'invoices');

    /** @var \Hubleto\App\Community\Workflow\Manager $workflowManager */
    $workflowManager = $this->getService(\Hubleto\App\Community\Workflow\Manager::class);
    $workflowManager->addWorkflowGroup($this, 'invoices', Workflow::class);

    /** @var Counter */
    $counter = $this->getService(Counter::class);

    $this->preparedItemsCount = $counter->preparedItems();
    $this->notPaidInvoicesCount = $counter->notPaidInvoices();
    $this->dueAndNotPaidInvoicesCount = $counter->dueAndNotPaidInvoices();
    $this->unsentInvoicesCount = $counter->unsentInvoices();

  }

  /**
   * [Description for upgradeSchema]
   *
   * @param int $round
   *
   * @return void
   *
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Payment::class)->upgradeSchema();
      $this->getModel(Models\PaymentMethod::class)->upgradeSchema();
      $this->getModel(Models\Invoice::class)->upgradeSchema();
      $this->getModel(Models\Profile::class)->upgradeSchema();
      $this->getModel(Models\Item::class)->upgradeSchema();
    }
  }

  public function generateDemoData(): void
  {
    /** @var Models\PaymentMethod */
    $mPaymentMethod = $this->getModel(Models\PaymentMethod::class);

    /** @var Models\Profile */
    $mProfile = $this->getModel(Models\Profile::class);

    $idPaymentMethod = $mPaymentMethod->record->recordCreate(['name' => $this->translate('bank transfer')])['id'];

    $mProfile->record->recordCreate([
      'name' => $this->translate('Test Profile 1'),
      'numbering_pattern' => 'T/YYYYNNNN',
      'is_default' => true,
      'id_payment_method' => $idPaymentMethod,
      'invoice_type_prefixes' => json_encode([
          Models\Invoice::TYPES[Models\Invoice::TYPE_PROFORMA] => 'PRO',
          Models\Invoice::TYPES[Models\Invoice::TYPE_ADVANCE] => 'ADV',
          Models\Invoice::TYPES[Models\Invoice::TYPE_STANDARD] => 'INV',
          Models\Invoice::TYPES[Models\Invoice::TYPE_CREDIT_NOTE] => 'CRD',
          Models\Invoice::TYPES[Models\Invoice::TYPE_DEBIT_NOTE] => 'DBT',
    ]),
      'mail_send_invoice_subject' => 'Invoice nr. {{ number }}',
      'mail_send_invoice_body' => 'Dear customer, see attached invoice nr. {{ number }}.',
      'mail_send_due_warning_subject' => 'Notification on due invoice nr. {{ number }}',
      'mail_send_due_warning_body' => 'Dear customer, this is a kindly reminder on due invoice nr. {{ number }}.',
    ]);
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
      $this->preparedItemsCount
      + $this->dueAndNotPaidInvoicesCount
      + $this->unsentInvoicesCount
    ;
  }

  /**
   * [Description for renderPriorityNotifications]
   *
   * @return string
   *
   */
  public function renderPriorityNotifications(): string
  {

    return 
      ($this->notPaidInvoicesCount > 0 ? '
        <a
          href="' . $this->env()->projectUrl . '/invoices?filters%5BfIssued%5D=0&filters%5BfPaid%5D=2"
          class="block alert alert-warning"
        >' . $this->translate('Not paid') . ': ' . $this->notPaidInvoicesCount . '</a>
      ' : '')
      . ($this->dueAndNotPaidInvoicesCount > 0 ? '
        <a
          href="' . $this->env()->projectUrl . '/invoices?filters%5BfIssued%5D=0&filters%5BfDue%5D=1&filters%5BfPaid%5D=2"
          class="block alert alert-danger"
        >' . $this->translate('Due and not paid') . ': ' . $this->dueAndNotPaidInvoicesCount . '</a>
      ' : '')
      . ($this->unsentInvoicesCount > 0 ? '
        <a
          href="' . $this->env()->projectUrl . '/invoices?filters%5BfIssued%5D=0&filters%5BfSent%5D=2"
          class="block alert alert-danger"
        >' . $this->translate('Not sent') . ': ' . $this->unsentInvoicesCount . '</a>
      ' : '')
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
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/invoices">
          <span class="icon"><i class="fas fa-file-invoice"></i></span>
          <span class="text">' . $this->translate('Invoices') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/items">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Items') . '</span>
        ' . ($this->preparedItemsCount > 0 ? '<span class="badge badge-danger ml-auto">' . $this->preparedItemsCount . '</span>' : '') . '
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/invoices/payments">
          <span class="icon"><i class="fas fa-euro-sign"></i></span>
          <span class="text">' . $this->translate('Payments') . '</span>
        </a>
        <div class="mt-4">
          <b>' . $this->translate('Settings') . '</b>
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

  /**
   * Implements fulltext search functionality for tasks
   *
   * @param array $expressions List of expressions to be searched and glued with logical 'or'.
   *
   * @return array
   *
   */
  public function search(array $expressions): array
  {
    $mInvoice = $this->getModel(Models\Invoice::class);
    $qInvoices = $mInvoice->record->prepareReadQuery();

    foreach ($expressions as $e) {
      $qInvoices = $qInvoices->having(function($q) use ($e) {
        $e = (string) $e;
        $eAsInt = preg_replace('/[^0-9]/', '', $e);
        $q->orHaving('invoices.number', 'like', '%' . $e . '%');
        if (strlen($eAsInt) > 3) {
          $q->orHaving('invoices.vs', 'like', '%' . $eAsInt . '%');
        }
      });
    }

    $invoices = $qInvoices->get()->toArray();
    $results = [];

    foreach ($invoices as $invoice) {
      $results[] = [
        "id" => $invoice['id'],
        "label" => $invoice['number'] . ' ' . $invoice['vs'],
        "url" => 'invoices/' . $invoice['id'],
        "description" => Models\Invoice::TYPES[$invoice['type']],
      ];
    }

    return $results;
  }

}