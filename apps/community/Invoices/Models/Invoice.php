<?php

namespace HubletoApp\Community\Invoices\Models;

use \HubletoApp\Community\Customers\Models\Company;
use \HubletoApp\Community\Settings\Models\User;
use \HubletoApp\Community\Settings\Models\InvoiceProfile;

class Invoice extends \ADIOS\Core\Model {
  public string $table = 'invoices';
  public ?string $lookupSqlValue = '{%TABLE%}.number';
  public string $eloquentClass = Eloquent\Invoice::class;

  public array $relations = [
    'CUSTOMER' => [ self::BELONGS_TO, Company::class, "id_customer" ],
    'PROFILE' => [ self::BELONGS_TO, InvoiceProfile::class, "id_profile" ],
    'ISSUED_BY' => [ self::BELONGS_TO, User::class, "id_issued_by" ],
    'ITEMS' => [ self::HAS_MANY, InvoiceItem::class, "id_invoice", "id" ],
  ];

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      "id_profile" => [ "type" => "lookup", "model" => InvoiceProfile::class, "title" => $this->translate("Profile") ],
      "id_issued_by" => [ "type" => "lookup", "model" => User::class, "title" => $this->translate("Issued by") ],
      "id_customer" => [ "type" => "lookup", "model" => Company::class, "title" => $this->translate("Customer") ],
      "number" => [ "type" => "varchar", "title" => $this->translate("Number"), "description" => $this->translate("TIP: Number is auto-generated based on the pattern configured in the profile.") ],
      "vs" => [ "type" => "varchar", "title" => $this->translate("Variable symbol") ],
      "cs" => [ "type" => "varchar", "title" => $this->translate("Constant symbol") ],
      "ss" => [ "type" => "varchar", "title" => $this->translate("Specific symbol") ],
      "date_issue" => [ "type" => "date", "title" => $this->translate("Issued") ],
      "date_delivery" => [ "type" => "date", "title" => $this->translate("Delivered") ],
      "date_due" => [ "type" => "date", "title" => $this->translate("Due") ],
      "date_payment" => [ "type" => "date", "title" => $this->translate("Payment") ],
      "notes" => [ "type" => "text", "title" => $this->translate("Notes") ],
    ]));
  }

  public function onBeforeCreate(array $record): array
  {

    $mInvoiceProfile = new InvoiceProfile($this->main);

    $invoicesThisYear = $this->eloquent->whereYear('date_delivery', date('Y'))->get()->toArray();
    $profil = $mInvoiceProfile->eloquent->where('id', $record['id_profile'])->first()->toArray();

    $record['number'] = $profil['numbering_pattern'] ?? '{YYYY}{NNNN}';
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

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
  
    $query = parent::prepareLoadRecordQuery($includeRelations, $maxRelationLevel, $query, $level);

    $idCustomer = $this->main->urlParamAsInteger('idCustomer');
    if ($idCustomer > 0) $query->where('id_customer', $idCustomer);

    $idProfile = $this->main->urlParamAsInteger('idProfile');
    if ($idProfile > 0) $query->where('id_profil', $idProfile);

    if ($this->main->isUrlParam('number')) $query->where('number', 'like', '%' . $this->main->urlParamAsString('number') . '%');
    if ($this->main->isUrlParam('vs')) $query->where('vs', 'like', '%' . $this->main->urlParamAsString('vs') . '%');

    if ($this->main->isUrlParam('dateIssueFrom')) $query->whereDate('date_issue', '>=', $this->main->urlParamAsString('dateIssueFrom'));
    if ($this->main->isUrlParam('dateIssueTo')) $query->whereDate('date_issue', '<=', $this->main->urlParamAsString('dateIssueTo'));
    if ($this->main->isUrlParam('dateDeliveryFrom')) $query->whereDate('date_delivery', '>=', $this->main->urlParamAsString('dateDeliveryFrom'));
    if ($this->main->isUrlParam('dateDeliveryTo')) $query->whereDate('date_delivery', '<=', $this->main->urlParamAsString('dateDeliveryTo'));
    if ($this->main->isUrlParam('dateTueFrom')) $query->whereDate('date_due', '>=', $this->main->urlParamAsString('dateTueFrom'));
    if ($this->main->isUrlParam('dateTueTo')) $query->whereDate('date_due', '<=', $this->main->urlParamAsString('dateTueTo'));
    if ($this->main->isUrlParam('datePaymentFrom')) $query->whereDate('date_payment', '>=', $this->main->urlParamAsString('datePaymentFrom'));
    if ($this->main->isUrlParam('datePaymentTo')) $query->whereDate('date_payment', '<=', $this->main->urlParamAsString('datePaymentTo'));

    $query
      ->first()
      ?->toArray()
    ;

    return $query;
  }

  public function onAfterLoadRecord(array $record): array {
    $vatPercent = 20;

    $total = 0;
    foreach ($record['ITEMS'] as $key => $item) {
      $itemPrice = (float) $item['unit_price'] * (float) $cinnost['amount'];
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
}