<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class Country extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'countries';
  public string $eloquentClass = Eloquent\Country::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'name' => [
        'type' => 'varchar',
        'title' => 'Country Name',
      ],
      'code' => [
        'type' => 'varchar',
        'byte_size' => '5',
        'title' => 'Code',
      ],
    ]);
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe();
    $description['ui']['title'] = 'Countries';
    return $description;
  }

}
