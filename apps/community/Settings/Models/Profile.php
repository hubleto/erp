<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;

class Profile extends \HubletoMain\Core\Model
{
  public string $table = 'profiles';
  public string $eloquentClass = Eloquent\Profile::class;
  public ?string $lookupSqlValue = '{%TABLE%}.company';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'company' => (new Varchar($this, $this->translate('Company')))->setRequired(),
    ]);
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();

    $description->ui['title'] = 'Profiles';
    $description->ui['addButtonText'] = 'Add Profile';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
