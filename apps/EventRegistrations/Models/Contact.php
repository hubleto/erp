<?php

namespace HubletoApp\Community\EventRegistrations\Models;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;

class Contact extends \HubletoMain\Model
{
  public string $table = 'my_app_contacts';
  public string $recordManagerClass = RecordManagers\Contact::class;

  public array $relations = [
    'MANAGER' => [ self::BELONGS_TO, User::class, 'id_manager', 'id' ]
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'id_manager' => (new Lookup($this, $this->translate('Manager'), User::class)),
    ]);
  }

}
