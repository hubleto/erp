<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;

class Country extends \HubletoMain\Core\Model
{
  public string $table = 'countries';
  public string $eloquentClass = Eloquent\Country::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'name' => (new Varchar($this, $this->translate('Country'))),
      'code' => (new Varchar($this, $this->translate('Code'))),
    ]);
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();

    $description->ui['title'] = 'Countries';
    $description->ui['addButtonText'] = 'Add Country';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
