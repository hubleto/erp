<?php

namespace Hubleto\App\Community\Campaigns\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Datetime;
use Hubleto\Framework\Db\Column\Varchar;

class Click extends \Hubleto\Erp\Model
{
  public string $table = 'campaigns_clicks';
  public string $recordManagerClass = RecordManagers\Click::class;

  public array $relations = [
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id' ],
    'RECIPIENT' => [ self::HAS_ONE, Recipient::class, 'id_recipient', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
      'id_recipient' => (new Lookup($this, $this->translate('Recipient'), Recipient::class)),
      'url' => (new Varchar($this, $this->translate('Url'))),
      'datetime_clicked' => (new Datetime($this, $this->translate('Clicked'))),
    ]);
  }

}
