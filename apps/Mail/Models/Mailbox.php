<?php

namespace HubletoApp\Community\Mail\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class Mailbox extends \HubletoMain\Model
{
  public string $table = 'mails_mailboxes';
  public string $recordManagerClass = RecordManagers\Mailbox::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public array $relations = [
    'ACCOUNT' => [ self::BELONGS_TO, Account::class, 'id_account', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_account' => (new Lookup($this, $this->translate('Account'), Account::class))->setReadonly(),
      'name' => (new Varchar($this, $this->translate('Name'))),
    ]);
  }

}
