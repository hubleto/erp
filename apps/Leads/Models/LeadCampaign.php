<?php

namespace Hubleto\App\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\App\Community\Campaigns\Models\Campaign;

class LeadCampaign extends \Hubleto\Erp\Model
{
  public string $table = 'leads_campaigns';
  public string $recordManagerClass = RecordManagers\LeadCampaign::class;
  public ?string $lookupSqlValue = '{%TABLE%}.id';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead', 'id'],
    'CAMPAIGN' => [ self::BELONGS_TO, Campaign::class, 'id_campaign', 'id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'id_campaign' => (new Lookup($this, $this->translate('Campaign'), Campaign::class))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Campaign Leads';
    $description->ui["addButtonText"] = $this->translate("Add lead");

    if ($this->router()->urlParamAsInteger('idCampaign') > 0) {
      $description->columns = [];
      $description->inputs = [];
      $description->ui = [];
    }

    return $description;
  }
}
