<?php

namespace Hubleto\App\Community\Invoices\Models;

use Hubleto\Framework\Db\Column\Varchar;

class PaymentMethod extends \Hubleto\Erp\Model
{
  public string $table = 'invoice_payment_methods';
  public string $recordManagerClass = RecordManagers\PaymentMethod::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';
  public ?string $lookupUrlDetail = 'invoices/payment-methods/{%ID%}';
  public ?string $lookupUrlAdd = 'invoices/payment-methods/add';

  public array $relations = [
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
      'name' => (new Varchar($this, $this->translate('Name'))),
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
    $description->ui['addButtonText'] = $this->translate("Add payment method");
    $description->show(['header', 'fulltextSearch', 'columnSearch', 'moreActionsButton']);
    $description->hide(['footer']);

    return $description;
  }

}
