<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;

class Profile extends \HubletoMain\Core\Model
{
  public string $table = 'profiles';
  public string $eloquentClass = Eloquent\Profile::class;
  public ?string $lookupSqlValue = '{%TABLE%}.company';

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'company' => (new Varchar($this, $this->translate('Company')))->setRequired(),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Profiles';
    $description->ui['addButtonText'] = 'Add Profile';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
