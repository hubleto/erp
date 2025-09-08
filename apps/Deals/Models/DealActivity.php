<?php

namespace Hubleto\App\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Contacts\Models\Contact;

class DealActivity extends \Hubleto\App\Community\Calendar\Models\Activity
{
  public string $table = 'deal_activities';
  public string $recordManagerClass = RecordManagers\DealActivity::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
    ]);
  }

}
