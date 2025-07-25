<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Contacts\Models\Contact;

class LeadActivity extends \HubletoApp\Community\Calendar\Models\Activity
{
  public string $table = 'lead_activities';
  public string $recordManagerClass = RecordManagers\LeadActivity::class;

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class)),
    ]);
  }
}
