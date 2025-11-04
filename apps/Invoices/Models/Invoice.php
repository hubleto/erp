<?php

namespace Hubleto\App\Community\Invoices\Models;


use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Settings\Models\InvoiceProfile;

use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\Settings\Models\Currency;
use Hubleto\App\Community\Auth\Models\User;

use Hubleto\Framework\Helper;

class Invoice extends \Hubleto\Erp\Model {

  const TYPE_PROFORMA = 1;
  const TYPE_ADVANCE = 2;
  const TYPE_STANDARD = 3;
  const TYPE_CREDIT_NOTE = 4;
  const TYPE_DEBIT_NOTE = 5;

  const TYPES = [
    1 => 'Proforma',
    2 => 'Advance',
    3 => 'Standard',
    4 => 'Credit Note',
    5 => 'Debit Note',
  ];

  public string $table = 'invoices';
  public ?string $lookupSqlValue = '{%TABLE%}.number';
  public string $recordManagerClass = RecordManagers\Invoice::class;

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, "id_customer" ],
    'PROFILE' => [ self::BELONGS_TO, InvoiceProfile::class, "id_profile" ],
    'ISSUED_BY' => [ self::BELONGS_TO, User::class, "id_issued_by" ],
    'TEMPLATE' => [ self::HAS_ONE, Template::class, 'id', 'id_template'],
    'CURRENCY' => [ self::HAS_ONE, Currency::class, 'id', 'id_currency'],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],

    'ITEMS' => [ self::HAS_MANY, InvoiceItem::class, "id_invoice", "id" ],
  ];

  /**
   * [Description for describeColumns]
   *
   * @return array
   * 
   */
  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_profile' => (new Lookup($this, $this->translate('Supplier'), InvoiceProfile::class))->setDefaultVisible(),
      'id_issued_by' => (new Lookup($this, $this->translate('Issued by'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),
      'type' => (new Integer($this, $this->translate('Type')))->setEnumValues(self::TYPES),
      'number' => (new Varchar($this, $this->translate('Number')))->setDefaultVisible(),
      'vs' => (new Varchar($this, $this->translate('Variable symbol')))->setDefaultVisible(),
      'cs' => (new Varchar($this, $this->translate('Constant symbol'))),
      'ss' => (new Varchar($this, $this->translate('Specific symbol'))),
      'date_issue' => (new Date($this, $this->translate('Issued')))->setDefaultVisible(),
      'date_delivery' => (new Date($this, $this->translate('Delivered'))),
      'date_due' => (new Date($this, $this->translate('Due')))->setDefaultVisible(),
      'date_payment' => (new Date($this, $this->translate('Paid')))->setDefaultVisible(),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), Currency::class)),
      'total_excl_vat' => new Decimal($this, $this->translate('Total excl. VAT'))->setReadonly(),
      'total_incl_vat' => new Decimal($this, $this->translate('Total incl. VAT'))->setReadonly(),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
    ]);
  }

  /**
   * [Description for describeTable]
   *
   * @return \Hubleto\Framework\Description\Table
   * 
   */
  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate("Add invoice");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    $description->ui['filters'] = [
      'fInvoiceWorkflowStep' => Workflow::buildTableFilterForWorkflowSteps($this, 'Status'),
    ];

    return $description;
  }

  /**
   * [Description for describeForm]
   *
   * @return \Hubleto\Framework\Description\Form
   * 
   */
  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    $description->defaultValues = [
      'id_issued_by' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(),
      'issued' => date('Y-m-d H:i:s'),
    ];
    return $description;
  }

  /**
   * [Description for recalculateTotalsForInvoice]
   *
   * @param int $idInvoice
   * 
   * @return void
   * 
   */
  public function recalculateTotalsForInvoice(int $idInvoice): void
  {
    if ($idInvoice <= 0) return;

    $totalExclVat = 0;
    $totalInclVat = 0;

    $mInvoiceItem = $this->getService(InvoiceItem::class);
    $items = $mInvoiceItem->record->where('id_invoice', $idInvoice)->get();

    foreach ($items as $item) {
      $totalExclVat += $item->price_excl_vat;
      $totalInclVat += $item->price_incl_vat;
    }

    $this->record->find($idInvoice)->update([
      "total_excl_vat" => $totalExclVat,
      "total_incl_vat" => $totalInclVat,
    ]);
  }

  /**
   * [Description for onBeforeCreate]
   *
   * @param array $record
   * 
   * @return array
   * 
   */
  public function onBeforeCreate(array $record): array
  {

    $mInvoiceProfile = $this->getService(InvoiceProfile::class);

    $invoicesThisYear = (array) $this->record->whereYear('date_delivery', date('Y'))->get()->toArray();
    $profil = $mInvoiceProfile->record->where('id', $record['id_profile'])->first()->toArray();

    $record['number'] = (string) ($profil['numbering_pattern'] ?? '{YYYY}{NNNN}');
    $record['number'] = str_replace('{YY}', date('y'), $record['number']);
    $record['number'] = str_replace('{YYYY}', date('Y'), $record['number']);
    $record['number'] = str_replace('{NN}', str_pad((string) (count($invoicesThisYear) + 1), 2, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('{NNN}', str_pad((string) (count($invoicesThisYear) + 1), 3, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('{NNNN}', str_pad((string) (count($invoicesThisYear) + 1), 4, '0', STR_PAD_LEFT), $record['number']);

    $record['vs'] = $record['number'];
    $record['cs'] = "0308";
    $record['date_issue'] = date("Y-m-d");
    $record['date_delivery'] = date("Y-m-d");
    $record['date_due'] = date("Y-m-d", strtotime("+14 days"));

    return $record;
  }

  /**
   * [Description for onAfterCreate]
   *
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterCreate(array $savedRecord): array
  {
    /** @var Workflow */
    $mWorkflow = $this->getModel(Workflow::class);

    list($defaultWorkflow, $idWorkflow, $idWorkflowStep) = $mWorkflow->getDefaultWorkflowInGroup('invoices');

    $savedRecord['id_workflow'] = $idWorkflow;
    $savedRecord['id_workflow_step'] = $idWorkflowStep;

    $this->record->recordUpdate($savedRecord);

    $this->recalculateTotalsForInvoice((int) $savedRecord['id']);

    return $savedRecord;
  }

  /**
   * [Description for onAfterUpdate]
   *
   * @param array $originalRecord
   * @param array $savedRecord
   * 
   * @return array
   * 
   */
  public function onAfterUpdate(array $originalRecord, array $savedRecord): array
  {
    $savedRecord = parent::onAfterUpdate($originalRecord, $savedRecord);

    $this->recalculateTotalsForInvoice((int) $savedRecord['id']);

    return $savedRecord;
  }

  /**
   * [Description for onAfterLoadRecord]
   *
   * @param array $record
   * 
   * @return array
   * 
   */
  public function onAfterLoadRecord(array $record): array {
    $vatPercent = 20;

    $total = 0;
    foreach ($record['ITEMS'] as $key => $item) { //@phpstan-ignore-line
      $unitPrice = (float) ($item['unit_price'] ?? 0);
      $amount = (float) ($item['amount'] ?? 0);
      $itemPrice = $unitPrice * $amount;
      $itemVat = $itemPrice*$vatPercent/100;
      $total += $itemPrice;

      $record['ITEMS'][$key]['SUMMARY'] = [
        'totalExcludingVat' => $itemPrice,
        'vat' => $itemVat,
        'totalIncludingVat' => $itemPrice + $itemVat,
      ];
    }

    $totalExclVat = $total;
    $vat = $totalExclVat*$vatPercent/100;

    $record['SUMMARY'] = [
      'totalExcludingVat' => $totalExclVat,
      'vat' => $vat,
      'totalIncludingVat' => $totalExclVat + $vat,
    ];

    return $record;

  }

  /**
   * Generates invoice and return ID of generated invoice
   *
   * @param InvoiceInterface $invoice
   * 
   * @return int ID of generated invoice
   * 
   */
  public function generateInvoice(Dto\Invoice $invoice): int
  {
    $idInvoice = $this->record->recordCreate([
      'id_profile' => $invoice->idProfile,
      'id_issued_by' => $invoice->idIssuedBy,
      'id_customer' => $invoice->idCustomer,
      'number' => $invoice->number,
      'vs' => $invoice->vs,
      'cs' => $invoice->cs,
      'ss' => $invoice->ss,
      'date_issue' => $invoice->date_issue,
      'date_delivery' => $invoice->date_delivery,
      'date_due' => $invoice->date_due,
      'date_payment' => $invoice->date_payment,
      'notes' => $invoice->notes,
    ])['id'];

    return $idInvoice;
  }

  /**
   * Generates PDF document from given invoice and returns ID of generated document
   *
   * @param int $idInvoice Invoice for which the PDF should be generated.
   * 
   * @return int ID of generated document.
   * 
   */
  public function generatePdf(int $idInvoice): int
  {
    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    $invoice = $mInvoice->record->prepareReadQuery()->where('invoices.id', $idInvoice)->first();
    if (!$invoice) throw new \Exception('Invoice was not found.');

    $mTemplate = $this->getService(Template::class);
    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $invoice->id_template)->first();
    if (!$template) throw new \Exception('Template was not found.');

    $vars = $invoice->toArray();
    $vars['now'] = new \DateTimeImmutable()->format('Y-m-d H:i:s');

    /** @var Generator */
    $generator = $this->getService(Generator::class);
    $idDocument = $generator->generatePdfFromTemplate(
      $template->id,
      'invoice-' . Helper::str2url($invoice->number) . '.pdf',
      $vars
    );

    if ($idDocument > 0) {
      $mInvoiceDocument = $this->getService(InvoiceDocument::class);
      $mInvoiceDocument->record->recordCreate([
        'id_invoice' => $idInvoice,
        'id_document' => $idDocument,
      ]);
    }

    return $idDocument;
  }

}