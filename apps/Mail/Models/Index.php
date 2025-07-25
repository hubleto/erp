<?php

namespace HubletoApp\Community\Mail\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Settings\Models\User;

class Index extends \Hubleto\Framework\Models\Model
{
  public string $table = 'mails_index';
  public string $recordManagerClass = RecordManagers\Index::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id_mail';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_mail' => (new Lookup($this, $this->translate('Mail'), Mail::class)),
      'id_from' => (new Lookup($this, $this->translate('From'), User::class)),
      'id_to' => (new Lookup($this, $this->translate('To'), User::class)),
      'id_cc' => (new Lookup($this, $this->translate('Cc'), User::class)),
      'id_bcc' => (new Lookup($this, $this->translate('Bcc'), User::class)),
    ]);
  }
}
