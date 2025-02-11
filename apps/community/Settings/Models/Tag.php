<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Color;

class Tag extends \HubletoMain\Core\Model
{
  public string $table = 'tags';
  public string $eloquentClass = Eloquent\Tag::class;
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

    $description->ui['title'] = 'Tags';
    $description->ui['addButtonText'] = 'Add Tag';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
