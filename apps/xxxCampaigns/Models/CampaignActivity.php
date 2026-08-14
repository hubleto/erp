<?php

namespace Hubleto\App\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;

class CampaignActivity extends \Hubleto\App\Community\Calendar\Models\Activity
{
  public string $table = 'campaign_activities';
  public string $recordManagerClass = RecordManagers\CampaignActivity::class;

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
    ]);
  }
}
