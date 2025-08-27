<?php

namespace HubletoApp\Community\Cloud\Models;

use Hubleto\Framework\Db\Column\Decimal;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

class Discount extends \HubletoMain\Model
{
  public string $table = 'cloud_discounts';
  public string $recordManagerClass = RecordManagers\Discount::class;

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'year' => (new Integer($this, $this->translate('Year')))->setRequired(),
      'month' => (new Integer($this, $this->translate('Month')))->setRequired(),
      'discount_percent' => (new Integer($this, $this->translate('Discount')))->setUnit('%'),
      'notes' => (new Varchar($this, $this->translate('Notes'))),
      'id_billing_account' => (new Lookup($this, $this->translate("Billing account"), BillingAccount::class)),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->columns['id'] = $this->columns['id'];
    $description->permissions['canCreate'] = false;
    $description->permissions['canUpdate'] = false;
    $description->permissions['canDelete'] = false;
    return $description;
  }

}
