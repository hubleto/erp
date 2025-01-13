<?php

namespace HubletoApp\Community\Settings\Models;

class Country extends \HubletoMain\Core\Model
{
  public string $table = 'countries';
  public string $eloquentClass = Eloquent\Country::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Country Name'),
      ],
      'code' => [
        'type' => 'varchar',
        'byte_size' => '5',
        'title' => $this->translate('Code'),
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Countries';
    $description['ui']['addButtonText'] = 'Add Country';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}
