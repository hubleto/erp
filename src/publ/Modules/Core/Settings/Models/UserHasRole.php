<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class UserHasRole extends \CeremonyCrmApp\Core\Model
{
  public string $table = 'user_has_roles';
  public string $eloquentClass = Eloquent\UserHasRole::class;

  public function columns(array $columns = []): array
  {
    return parent::columns([
      'id_user' => [
        'type' => 'lookup',
        'title' => $this->translate('User'),
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/User',
      ],
      'id_role' => [
        'type' => 'lookup',
        'title' => $this->translate('Role'),
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/UserRole',
      ],
    ]);
  }
}