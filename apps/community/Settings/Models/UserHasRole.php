<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Lookup;

class UserHasRole extends \HubletoMain\Core\Model
{
  public string $table = 'user_has_roles';
  public string $eloquentClass = Eloquent\UserHasRole::class;

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'id_user' => (new Lookup($this, $this->translate('User'), User::class)),
      'id_role' => (new Lookup($this, $this->translate('Role'), UserRole::class)),
    ]);
  }

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Role Assigments';
    $description->ui['addButtonText'] = 'Assign Roles';
    $description->ui['showHeader'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }
}