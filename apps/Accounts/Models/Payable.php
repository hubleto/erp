<?php

namespace Hubleto\App\Community\Accounts\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;

class Payable extends \Hubleto\Erp\Model
{
  public string $table = 'accounts_payable';
  public string $recordManagerClass = RecordManagers\Payable::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.id_transaction, \' \', \' (\', {%TABLE%}.amount, \')\')';

  public array $relations = [
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'bill_date' => new Date($this, $this->translate("Bill date"))->setRequired(),
      'due_date' => new Date($this, $this->translate("Due date"))->setRequired(),
      'amount' => new Integer($this, $this->translate("Amount"))->setRequired(),
      'status' => new Integer($this, $this->translate("Status"))->setEnumValues([
        0 => 'Pending',
        1 => 'Paid',
        2 => 'Overdue',
      ])->setRequired(),
      'description' => new Varchar($this, $this->translate("Description")),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Account Payables';
    $description->ui['addButtonText'] = 'Add a payable';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
