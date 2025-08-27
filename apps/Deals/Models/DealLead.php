<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Lookup;
use HubletoApp\Community\Leads\Models\Lead;

class DealLead extends \HubletoMain\Model
{
  public string $table = 'deals_leads';
  public string $recordManagerClass = RecordManagers\DealLead::class;

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal', 'id' ],
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id' ],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = 'Add deal';
    return $description;
  }

}
