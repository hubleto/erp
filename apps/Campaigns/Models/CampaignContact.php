<?php

namespace HubletoApp\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Contacts\Models\Contact;

class CampaignContact extends \HubletoMain\Model
{
  public string $table = 'campaigns_contacts';
  public string $recordManagerClass = RecordManagers\CampaignContact::class;

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'CONTACT' => [ self::BELONGS_TO, Contact::class, 'id_contact', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
      'id_contact' => (new Lookup($this, $this->translate('Contact'), Contact::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add campaign';
    return $description;
  }

}
