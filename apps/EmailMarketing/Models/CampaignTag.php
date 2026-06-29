<?php

namespace Hubleto\App\Community\EmailMarketing\Models;

use Hubleto\Framework\Db\Column\Lookup;

class CampaignTag extends \Hubleto\Erp\Model
{
  public string $table = 'email_marketing_campaign_tags';
  public string $recordManagerClass = RecordManagers\CampaignTag::class;

  public array $relations = [
    'TAG' => [ self::BELONGS_TO, Tag::class, 'id_tag', 'id' ],
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
      'id_tag' => (new Lookup($this, $this->translate('Tag'), Tag::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Campaign tags');
    return $description;
  }

}
