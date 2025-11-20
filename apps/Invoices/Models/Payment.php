<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Lookup;

class Payment extends \Hubleto\Erp\Model
{
  public string $table = 'invoice_payments';
  public string $recordManagerClass = RecordManagers\Payment::class;
  public ?string $lookupSqlValue = '{%TABLE%}.date_payment';
  public ?string $lookupUrlDetail = 'invoices/payments/{%ID%}';
  public ?string $lookupUrlAdd = 'invoices/payments/add';

  public array $relations = [
    'INVOICE' => [ self::BELONGS_TO, Invoice::class, "id_invoice" ],
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
      'id_invoice' => (new Lookup($this, $this->translate('Invoice'), Invoice::class)),
      'date_payment' => (new Date($this, $this->translate('Payment date')))->setDefaultVisible(),
      'amount' => (new Decimal($this, $this->translate('Amount')))->setDefaultVisible(),
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
    $description->ui['addButtonText'] = $this->translate("Add payment");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }

}
