<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;

class ContactType extends \HubletoMain\Core\Model
{
  public string $table = 'contact_types';
  public string $eloquentClass = Eloquent\ContactType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => (new Varchar($this, $this->translate('Type')))->setRequired(),
    ]));
  }

  public function tableDescribe(): \ADIOS\Core\Description\Table
  {
    $description = parent::tableDescribe();

    $description->ui['title'] = $this->translate('Contact Types');
    $description->ui['addButtonText'] = 'Add Contact Type';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}
