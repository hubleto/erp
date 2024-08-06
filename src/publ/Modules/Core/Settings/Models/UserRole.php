<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class UserRole extends \ADIOS\Core\Model {
  const ROLE_ADMINISTRATOR = 1;

  const USER_ROLES = [
    self::ROLE_ADMINISTRATOR => 'ADMINISTRATOR',
  ];

  public string $fullTableSqlName = 'user_roles';
  public string $table = 'user_roles';
  public string $eloquentClass = Eloquent\UserRole::class;
  public ?string $lookupSqlValue = "{%TABLE%}.role";

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'role' => [
        'type' => 'varchar',
        'title' => $this->translate('Role')
      ],
    ]);
  }
}