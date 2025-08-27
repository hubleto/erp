<?php

namespace HubletoApp\Community\Leads\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class LeadHistory extends \HubletoMain\Model
{
  public string $table = 'lead_histories';
  public string $recordManagerClass = RecordManagers\LeadHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'LEAD' => [ self::BELONGS_TO, Lead::class, 'id_lead','id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'change_date' => (new Date($this, $this->translate('Change Date')))->setRequired(),
      'id_lead' => (new Lookup($this, $this->translate('Lead'), Lead::class))->setRequired(),
      'description' => (new Varchar($this, $this->translate('Description')))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['showHeader'] = false;
    $description->ui['showFulltextSearch'] = false;
    $description->ui['showFooter'] = false;
    unset($description->columns['id_lead']);
    return $description;
  }

}
