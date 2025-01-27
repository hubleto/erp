<?php

namespace HubletoApp\Community\Settings\Models;

class UserHasRole extends \HubletoMain\Core\Model
{
  public string $table = 'user_has_roles';
  public string $eloquentClass = Eloquent\UserHasRole::class;

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'id_user' => [
        'type' => 'lookup',
        'title' => $this->translate('User'),
        'model' => User::class,
      ],
      'id_role' => [
        'type' => 'lookup',
        'title' => $this->translate('Role'),
        'model' => UserRole::class,
      ],
    ]);
  }
  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Role Assigments';
      $description['ui']['addButtonText'] = 'Assign Roles';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    return $description;
  }
}