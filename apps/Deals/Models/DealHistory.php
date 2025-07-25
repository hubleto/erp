<?php

namespace HubletoApp\Community\Deals\Models;

use Hubleto\Framework\Db\Column\Date;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Varchar;

class DealHistory extends \Hubleto\Framework\Models\Model
{
  public string $table = 'deal_histories';
  public string $recordManagerClass = RecordManagers\DealHistory::class;
  public ?string $lookupSqlValue = '{%TABLE%}.description';

  public array $relations = [
    'DEAL' => [ self::BELONGS_TO, Deal::class, 'id_deal','id'],
  ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'change_date' => (new Date($this, $this->translate('Change Date')))->setRequired(),
      'id_deal' => (new Lookup($this, $this->translate('Deal'), Deal::class))->setRequired(),
      'description' => (new Varchar($this, $this->translate('Description')))->setRequired(),
    ]);
  }

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();
    $description->ui['title'] = $this->translate('Deals');
    $description->ui['addButtonText'] = $this->translate('Add Deal');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;
    unset($description->columns['id_deal']);
    return $description;
  }

}
