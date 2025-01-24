<?php

namespace HubletoApp\Community\Settings\Models;

class ContactType extends \HubletoMain\Core\Model
{
  public string $table = 'contact_types';
  public string $eloquentClass = Eloquent\ContactType::class;
  public ?string $lookupSqlValue = '{%TABLE%}.name';

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'name' => [
        'type' => 'varchar',
        'title' => $this->translate('Name'),
        'required' => true,
      ],
    ]));
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = $this->translate('Contact Types');
    $description['ui']['addButtonText'] = 'Add Contact Type';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;

    return $description;
  }

}
