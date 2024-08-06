<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class User extends \ADIOS\Models\User {
  public string $fullTableSqlName = 'users';
  public string $table = 'users';
  public string $eloquentClass = Eloquent\User::class;

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'email' => [
        'type' => 'varchar',
        'title' => $this->translate('Email'),
        'show' => true,
      ],
      'id_active_profile' => [
        'type' => 'lookup',
        'model' => 'CeremonyCrmApp/Modules/Core/Settings/Models/Profile',
        'title' => $this->translate('Active profile'),
        'show' => true,
      ],
    ]));
  }


  public function getQueryForUser(int $idUser) {
    return $this->eloquent
      ->with('ROLES')
      ->with('PROFILE')
      ->where('id', $idUser)
      ->where('is_active', '<>', 0)
    ;
  }


  public function loadUser(int $idUser) {
    $user = $this->getQueryForUser($idUser)->first()?->toArray();

    $tmpRoles = [];
    foreach ($user['ROLES'] ?? [] as $role) {
      $tmpRoles[] = (int) $role['pivot']['id_role'];
    }
    $user['ROLES'] = $tmpRoles;

    return $user;
  }

  public function tableParams(array $params = []): array {
    $params = parent::tableParams();
    $params['title'] = 'Users';
    return $params;
  }

}