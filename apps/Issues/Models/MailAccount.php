<?php

namespace HubletoApp\Community\Issues\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Json;

use HubletoApp\Community\Mail\Models\Account;

class MailAccount extends \HubletoMain\Model
{
  public string $table = 'issues_mail_accounts';
  public string $recordManagerClass = RecordManagers\MailAccount::class;

  public array $relations = [
    'MAIL_ACCOUNT' => [ self::HAS_MANY, Account::class, 'id_mail_account', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_mail_account' => (new Lookup($this, $this->translate('Mail account (requires Mail app)'), Account::class))->setRequired(),
      'settings' => (new Json($this, $this->translate('Configuration')))->setRequired(),
      'notes' => (new Varchar($this, $this->translate('Notes'))),
    ]);
  }

}
