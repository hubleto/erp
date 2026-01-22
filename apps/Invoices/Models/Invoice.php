<?php

namespace Hubleto\App\Community\Invoices\Models;


use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Currency;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Text;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\File;
use Hubleto\Framework\Db\Column\Virtual;

use Hubleto\App\Community\Customers\Models\Customer;

use Hubleto\App\Community\Workflow\Models\Workflow;
use Hubleto\App\Community\Workflow\Models\WorkflowStep;
use Hubleto\App\Community\Documents\Generator;
use Hubleto\App\Community\Settings\Models\Currency as CurrencyModel;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Documents\Models\Template;
use Hubleto\App\Community\Suppliers\Models\Supplier;
use Hubleto\Framework\Helper;

class Invoice extends \Hubleto\Erp\Model {

  const TYPE_PROFORMA = 1;
  const TYPE_ADVANCE = 2;
  const TYPE_STANDARD = 3;
  const TYPE_CREDIT_NOTE = 4;
  const TYPE_DEBIT_NOTE = 5;

  const TYPES = [
    self::TYPE_PROFORMA => 'Proforma',
    self::TYPE_ADVANCE => 'Advance',
    self::TYPE_STANDARD => 'Standard',
    self::TYPE_CREDIT_NOTE => 'Credit Note',
    self::TYPE_DEBIT_NOTE => 'Debit Note',
  ];

  const INBOUND_INVOICE = 1;
  const OUTBOUND_INVOICE = 2;

  public string $table = 'invoices';
  public ?string $lookupSqlValue = '{%TABLE%}.number';
  public string $recordManagerClass = RecordManagers\Invoice::class;
  public ?string $lookupUrlAdd = 'invoices/add';
  public ?string $lookupUrlDetail = 'invoices/{%ID%}';

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Customer::class, "id_customer" ],
    'SUPPLIER' => [ self::HAS_ONE, Supplier::class, 'id', 'id_supplier'],
    'PAYMENT_METHOD' => [ self::BELONGS_TO, PaymentMethod::class, "id_payment_method" ],
    'PROFILE' => [ self::BELONGS_TO, Profile::class, "id_profile" ],
    'ISSUED_BY' => [ self::BELONGS_TO, User::class, "id_issued_by" ],
    'CURRENCY' => [ self::HAS_ONE, CurrencyModel::class, 'id', 'id_currency'],
    'WORKFLOW' => [ self::HAS_ONE, Workflow::class, 'id', 'id_workflow'],
    'WORKFLOW_STEP' => [ self::HAS_ONE, WorkflowStep::class, 'id', 'id_workflow_step'],
    'TEMPLATE' => [ self::HAS_ONE, Template::class, 'id', 'id_template'],

    'ITEMS' => [ self::HAS_MANY, Item::class, "id_invoice", "id" ],
    'PAYMENTS' => [ self::HAS_MANY, Payment::class, "id_invoice", "id" ],
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
      'inbound_outbound' => (new Integer($this, $this->translate('Inbound / outbound')))->setEnumValues([
        self::INBOUND_INVOICE => $this->translate('Inbound'),
        self::OUTBOUND_INVOICE => $this->translate('Outbound'),
      ])->setEnumCssClasses([
        self::INBOUND_INVOICE => 'bg-lime-50 text-slate-700',
        self::OUTBOUND_INVOICE => 'bg-yellow-50 text-slate-700',
      ])->setDefaultValue(self::OUTBOUND_INVOICE)->setDefaultVisible(),
      'id_profile' => (new Lookup($this, $this->translate('Invoicing profile'), Profile::class))->setDefaultVisible(),
      'id_issued_by' => (new Lookup($this, $this->translate('Issued by'), User::class))->setReactComponent('InputUserSelect')->setDefaultVisible(),
      'id_payment_method' => (new Lookup($this, $this->translate('Payment method'), PaymentMethod::class)),

      // used for outbound invoices
      'id_customer' => (new Lookup($this, $this->translate('Customer'), Customer::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_CUSTOMER_DEFAULT_ICON),

      // used for inbound invoices
      'id_supplier' => (new Lookup($this, $this->translate('Supplier'), Supplier::class))->setDefaultVisible()->setIcon(self::COLUMN_ID_SUPPLIER_DEFAULT_ICON),

      'type' => (new Integer($this, $this->translate('Type')))->setEnumValues(self::TYPES)->setRequired()->setDefaultValue(3)->setDefaultVisible()->setEnumCssClasses([
        self::TYPE_PROFORMA => 'bg-blue-50 text-slate-700',
        self::TYPE_ADVANCE => 'bg-lime-50 text-slate-700',
        self::TYPE_STANDARD => 'bg-green-50 text-slate-700',
        self::TYPE_CREDIT_NOTE => 'bg-orange-50 text-slate-700',
        self::TYPE_DEBIT_NOTE => 'bg-violet-50 text-slate-700',
      ]),
      'number' => (new Varchar($this, $this->translate('Number')))->setDefaultVisible()->setDescription($this->translate('Leave empty to generate automatically.')),
      'number_external' => (new Varchar($this, $this->translate('External number')))->setDefaultVisible(),
      'description' => (new Varchar($this, $this->translate('Description of services/goods'))),
      'vs' => (new Varchar($this, $this->translate('Variable symbol')))->setDefaultVisible(),
      'cs' => (new Varchar($this, $this->translate('Constant symbol'))),
      'ss' => (new Varchar($this, $this->translate('Specific symbol'))),
      'date_issue' => (new Date($this, $this->translate('Issued')))->setDefaultVisible(),
      'date_delivery' => (new Date($this, $this->translate('Delivered'))),
      'date_due' => (new Date($this, $this->translate('Due')))->setDefaultVisible(),
      'date_payment' => (new Date($this, $this->translate('Paid')))->setDefaultVisible(),
      'date_sent' => (new Date($this, $this->translate('Sent')))->setDefaultVisible(),
      'id_currency' => (new Lookup($this, $this->translate('Currency'), CurrencyModel::class)),
      'total_excl_vat' => new Currency($this, $this->translate('Total excl. VAT'))->setDecimals(2)->setReadonly(),
      'total_incl_vat' => new Currency($this, $this->translate('Total incl. VAT'))->setDecimals(2)->setReadonly(),
      'total_payments' => new Currency($this, $this->translate('Total payments'))->setDecimals(2)->setReadonly(),
      'notes' => (new Text($this, $this->translate('Notes'))),
      'id_workflow' => (new Lookup($this, $this->translate('Workflow'), Workflow::class)),
      'id_workflow_step' => (new Lookup($this, $this->translate('Workflow step'), WorkflowStep::class))->setDefaultVisible(),
      'id_template' => (new Lookup($this, $this->translate('Template'), Template::class)),
      'pdf' => (new File($this, $this->translate('PDF'))),
      'virt_items' => (new Virtual($this, $this->translate('Items')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT
            CONCAT('[', GROUP_CONCAT(
              JSON_OBJECT(
                'item', item,
                'unit_price', unit_price,
                'amount', amount
              )
            ), ']')
          FROM `invoice_items` `ii`
          WHERE `ii`.`id_invoice` in (`invoices`.`id`)
        "),
      'virt_payments' => (new Virtual($this, $this->translate('Payments')))->setDefaultVisible()
        ->setProperty('sql', "
          SELECT
            CONCAT('[', GROUP_CONCAT(
              JSON_OBJECT(
                'date_payment', date_payment,
                'amount', amount
              )
            ), ']')
          FROM `invoice_payments` `ip`
          WHERE `ip`.`id_invoice` in (`invoices`.`id`)
        "),
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
    $filters = $this->router()->urlParamAsArray("filters");

    $description = parent::describeTable();
    $description->ui['addButtonText'] = $this->translate("Add invoice");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->show(['footer']);

    switch ($filters['fInboundOutbound'] ?? 0) {
      case self::INBOUND_INVOICE: $description->hideColumns(['id_customer']); break;
      case self::OUTBOUND_INVOICE: $description->hideColumns(['id_supplier']); break;
    }

    $description->addFilter('fInboundOutbound', [
      'title' => $this->translate('Inbound / Outbound'),
      'direction' => 'horizontal',
      'options' => [
        self::INBOUND_INVOICE => $this->translate('Inbound'),
        self::OUTBOUND_INVOICE => $this->translate('Outbound'),
      ]
    ]);

    $description->addFilter('fSent', [
      'title' => $this->translate('Sent / Not sent'),
      'direction' => 'horizontal',
      'options' => [
        1 => $this->translate('Sent'),
        2 => $this->translate('Not sent'),
      ]
    ]);

    $description->addFilter('fDue', [
      'title' => $this->translate('Due / Not due'),
      'direction' => 'horizontal',
      'options' => [
        1 => $this->translate('Due'),
        2 => $this->translate('Not due'),
      ]
    ]);

    $description->addFilter('fPaid', [
      'title' => $this->translate('Paid / Not paid'),
      'direction' => 'horizontal',
      'options' => [
        1 => $this->translate('Paid'),
        2 => $this->translate('Not paid'),
      ]
    ]);

    $description->addFilter('fIssued', [
      'title' => $this->translate('Issued'),
      'options' => [
        'thisMonth' => $this->translate('This month'),
        'lastMonth' => $this->translate('Last month'),
        'beforeLastMonth' => $this->translate('Month before last'),
        'thisYear' => $this->translate('This year'),
        'lastYear' => $this->translate('Last year'),
      ],
      'default' => 0,
    ]);

    $description->addFilter('fType', [
      'title' => $this->translate('Type'),
      'options' => self::TYPES
    ]);

    $description->addFilter('fInvoiceWorkflowStep', Workflow::buildTableFilterForWorkflowSteps($this, 'Workflow step'));

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
      'type' => self::TYPE_STANDARD,
    ];
    return $description;
  }

  public function getRelationsIncludedInLoadTableData(): array|null
  {
    return ['ITEMS'];
  }

  public function getMaxReadLevelForLoadTableData(): int
  {
    return 1;
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

    $invoice = $this->record->find($idInvoice);
    if (!$invoice) return;

    $totalExclVat = 0;
    $totalInclVat = 0;
    $totalPayments = 0;

    // items
    $mItem = $this->getService(Item::class);
    $items = $mItem->record->where('id_invoice', $idInvoice)->get();

    foreach ($items as $item) {
      $totalExclVat += $item->price_excl_vat;
      $totalInclVat += $item->price_incl_vat;
    }

    // payments
    $mPayment = $this->getService(Payment::class);
    $payments = $mPayment->record->where('id_invoice', $idInvoice)->get();
    $lastPaymentTs = 0;

    foreach ($payments as $payment) {
      $totalPayments += $payment->amount;
      $lastPaymentTs = max($lastPaymentTs, strtotime($payment->date_payment));
    }

    $dataToUpdate = [
      "total_excl_vat" => $totalExclVat,
      "total_incl_vat" => $totalInclVat,
      "total_payments" => $totalPayments,
    ];

    if (
      $totalInclVat > 0
      && $totalPayments >= $totalInclVat
      && count($payments) > 0
    ) {
      $dataToUpdate['date_payment'] = date("Y-m-d", $lastPaymentTs);
    }

    // update
    $invoice->update($dataToUpdate);
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

    $idProfile = (int) ($record['id_profile'] ?? 0);
    $idTemplate = (int) ($record['id_template'] ?? 0);
    $idPaymentMethod = (int) ($record['id_payment_method'] ?? 0);

    /** @var Profile */
    $mProfile = $this->getService(Profile::class);

    if ($idProfile <= 0) {
      $defaultProfile = $mProfile->record->where('is_default', 1)->first();
      if (!$defaultProfile) throw new \Exception($this->translate('Default invoicing profile is not set.'));
      $idProfile = (int) $defaultProfile->id;
    }
    if ($idProfile <= 0) {
      throw new \Exception('Invoice profile is required to create an invoice.');
    }

    $profile = $mProfile->record->where('id', $idProfile)->first();

    $record['id_profile'] = $idProfile;

    if ($record['inbound_outbound'] <= 0) $record['inbound_outbound'] = self::INBOUND_INVOICE;
    if ($record['type'] <= 0) $record['type'] = self::TYPE_STANDARD;
    if ($idTemplate <= 0) $record['id_template'] = (int) ($profile['id_template'] ?? 0);
    if ($idPaymentMethod <= 0) $record['id_payment_method'] = (int) ($profile['id_payment_method'] ?? 0);

    $numberingPattern = (string) ($profile->numbering_pattern ?? 'YYYY/NNNN');

    $invoicesThisYear = $this->record
      ->whereYear('date_delivery', date('Y'))
      ->where('id_profile', $idProfile)
      ->where('inbound_outbound', $record['inbound_outbound'])
      ->where('type', $record['type'])
      ->get()
    ;

    $dueDays = $profile['due_days'] ?? 14;
    if ($dueDays < 0) $dueDays = 0;

    // Extract start and end offset of the invoice number from the
    // numbering pattern of the invoicing profile
    $nPositionStart = -1;
    $nPositionEnd = -1;
    for ($n = 0; $n < strlen($numberingPattern); $n++) {
      $c = $numberingPattern[$n];
      if ($c == 'N' && $nPositionStart == -1) {
        $nPositionStart = $n;
      } else if ($c != 'N' && $nPositionStart >= 0) {
        $nPositionEnd = $n;
      }
    }
    if ($nPositionEnd == -1) $nPositionEnd = $n;

    // Extract highest number of the invoice
    $maxNumber = 0;
    foreach ($invoicesThisYear as $invoice) {
      $maxNumber = max($maxNumber, (int) substr($invoice->number, $nPositionStart, $nPositionEnd));
    }

    $invoiceTypePrefixes = @json_decode($profile['invoice_type_prefixes'], true);
    $recordTypeAsString = self::TYPES[$record['type']] ?? '';

    // Calculate number of the new invoice
    $record['number'] = $numberingPattern;
    $record['number'] = str_replace('T', $invoiceTypePrefixes[$recordTypeAsString] ?? '', $record['number']);
    $record['number'] = str_replace('YYYY', date('Y'), $record['number']);
    $record['number'] = str_replace('YY', date('y'), $record['number']);
    $record['number'] = str_replace('MM', date('m'), $record['number']);
    $record['number'] = str_replace('DD', date('d'), $record['number']);
    $record['number'] = str_replace('NNNNNN', str_pad((string) ($maxNumber + 1), 6, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('NNNNN', str_pad((string) ($maxNumber + 1), 5, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('NNNN', str_pad((string) ($maxNumber + 1), 4, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('NNN', str_pad((string) ($maxNumber + 1), 3, '0', STR_PAD_LEFT), $record['number']);
    $record['number'] = str_replace('NN', str_pad((string) ($maxNumber + 1), 2, '0', STR_PAD_LEFT), $record['number']);

    // Calculate other default values
    $record['id_currency'] = $profile['id_currency'] ?? 0;
    $record['vs'] = preg_replace('/[^0-9]/', '', $record['number']);
    $record['cs'] = '0308';
    $record['date_issue'] = date('Y-m-d');
    $record['date_delivery'] = date('Y-m-d');
    $record['date_due'] = date('Y-m-d', strtotime('+' . $dueDays . ' days'));

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
   * [Description for getPreviewVars]
   *
   * @param int $idInvoice
   * 
   * @return array
   * 
   */
  public function getPreviewVars(int $idInvoice): array
  {
    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    $invoice = $mInvoice->record->prepareReadQuery()->where('invoices.id', $idInvoice)->first();
    if (!$invoice) throw new \Exception('Invoice was not found.');

    $vars = $invoice->toArray();
    $vars['hubleto'] = $this;
    $vars['now'] = new \DateTimeImmutable()->format('Y-m-d H:i:s');

    unset($vars['CUSTOMER']['CONTACTS']);
    unset($vars['CUSTOMER']['OWNER']);
    unset($vars['CUSTOMER']['MANAGER']);
    unset($vars['CUSTOMER']['ACTIVITIES']);
    unset($vars['CUSTOMER']['DOCUMENTS']);
    unset($vars['CUSTOMER']['TAGS']);
    unset($vars['CUSTOMER']['LEADS']);
    unset($vars['CUSTOMER']['DEALS']);
    unset($vars['PROFILE']['TEMPLATE']);
    unset($vars['ISSUED_BY']['ROLES']);
    unset($vars['ISSUED_BY']['TEAMS']);
    unset($vars['ISSUED_BY']['DEFAULT_COMPANY']);
    unset($vars['WORKFLOW']);
    unset($vars['WORKFLOW_STEP']);
    unset($vars['TEMPLATE']);

    foreach ($vars['ITEMS'] as $key => $item) {
      unset($vars['ITEMS'][$key]['INVOICE']);
      unset($vars['ITEMS'][$key]['CUSTOMER']);
    }

    return $vars;

  }

  /**
   * [Description for getPreviewHtml]
   *
   * @param int $idInvoice
   * 
   * @return string
   * 
   */
  public function getPreviewHtml(int $idInvoice): string
  {

    $vars = $this->getPreviewVars($idInvoice);

    /** @var Template */
    $mTemplate = $this->getService(Template::class);

    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $vars['id_template'])->first();
    if (!$template) throw new \Exception('Template was not found.');

    /** @var Generator */
    $generator = $this->getService(Generator::class);
    return $generator->renderTemplate($vars['id_template'], $vars);
  }

  /**
   * Generates PDF document from given invoice and returns ID of generated document
   *
   * @param int $idInvoice Invoice for which the PDF should be generated.
   * 
   * @return int Output filename.
   * 
   */
  public function generatePdf(int $idInvoice): string
  {
    /** @var Invoice */
    $mInvoice = $this->getModel(Invoice::class);

    $invoice = $mInvoice->record->prepareReadQuery()
      ->with('CUSTOMER')
      ->where('invoices.id', $idInvoice)
      ->first()
    ;
    if (!$invoice) throw new \Exception('Invoice was not found.');

    /** @var Template */
    $mTemplate = $this->getService(Template::class);

    $template = $mTemplate->record->prepareReadQuery()->where('documents_templates.id', $invoice->id_template)->first();
    if (!$template) throw new \Exception('Template was not found.');

    $vars = $this->getPreviewVars($idInvoice);

    switch ($invoice->type) {
      case 1: $invoiceOutputFilename = 'Proforma Invoice'; break;
      case 2: $invoiceOutputFilename = 'Advance Invoice'; break;
      case 3: $invoiceOutputFilename = 'Invoice'; break;
      case 4: $invoiceOutputFilename = 'Credit Note'; break;
      case 5: $invoiceOutputFilename = 'Debit Note'; break;
    }

    $invoiceOutputFilename .=
      ' '
      . Helper::str2url($invoice->number)
      . ' '
      . Helper::str2url($invoice->CUSTOMER->name)
      . '.pdf'
    ;

    /** @var Generator */
    $generator = $this->getService(Generator::class);
    $generator->generatePdfFromTemplate(
      $template->id,
      $invoiceOutputFilename,
      $vars
    );

    $mInvoice->record->find($idInvoice)->update([
      'pdf' => $invoiceOutputFilename,
    ]);

    return $invoiceOutputFilename;
  }

}