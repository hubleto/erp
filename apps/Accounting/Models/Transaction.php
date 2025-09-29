<?php

namespace Hubleto\App\Community\Accounting\Models;

use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Varchar;

class Transaction extends \Hubleto\Erp\Model
{
  public string $table = 'accounting_transaction';
  public string $recordManagerClass = \Hubleto\App\Community\Accounting\Models\RecordManagers\Transaction::class;
  public ?string $lookupSqlValue = 'concat({%TABLE%}.description, \' \', \' (\', {%TABLE%}.amount, \')\')';

  public array $relations = [
//    'ACCOUNT_TYPE' => [ self::BELONGS_TO, AccountType::class, 'id_account_type', 'id'  ],
//    'ACCOUNT_SUBTYPE' => [ self::BELONGS_TO, AccountSubtype::class, 'id_account_subtype', 'id'  ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'bank' => new Varchar($this, $this->translate("Bank"))->setRequired(),
      'date' => new Date($this, $this->translate("Transaction date"))->setRequired(),
      'description' => new Varchar($this, $this->translate("Description"))->setRequired(),
      'amount' => new Integer($this, $this->translate('Amount'))->setRequired(),
      'isReconciled' => new Boolean($this, $this->translate('Is reconciled?'))->setDefaultValue(false)->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Transactions';
    $description->ui['addButtonText'] = 'Add a transaction';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    return $description;
  }
}
