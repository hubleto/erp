<?php

namespace HubletoApp\Community\Deals\Models;

use ADIOS\Core\Db\Column\Color;
use ADIOS\Core\Db\Column\Varchar;

class Tag extends \HubletoMain\Core\Models\Model
{
  public string $table = 'deal_tags';
  public string $recordManagerClass = RecordManagers\Tag::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'name' => (new Varchar($this, $this->translate('Name')))->setRequired(),
      'color' => (new Color($this, $this->translate('Color')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = $this->translate('Deal Tags');
    $description->ui['addButtonText'] = $this->translate('Add Deal Tag');
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
