<?php

namespace CeremonyCrmApp\Modules\Core\Settings\Models;

class User extends \ADIOS\Models\User
{
  const ENUM_LANGUAGES = [
    'cz' => 'Česky',
    'de' => 'Deutsch',
    'en' => 'English',
    'es' => 'Español',
    'fr' => 'Francais',
    'pl' => 'Polski',
    'sk' => 'Slovensky',
  ];

  public string $table = 'users';
  public string $eloquentClass = Eloquent\User::class;

  public ?string $lookupSqlValue = "{%TABLE%}.email";

  public function columns(array $columns = []): array
  {
    return parent::columns(array_merge($columns, [
      'first_name' => [
        'type' => 'varchar',
        'title' => $this->translate('First name'),
        'show' => true,
      ],
      'middle_name' => [
        'type' => 'varchar',
        'title' => $this->translate('Middle name'),
        'show' => true,
      ],
      'last_name' => [
        'type' => 'varchar',
        'title' => $this->translate('Last name'),
        'show' => true,
      ],
      'email' => [
        'type' => 'varchar',
        'title' => $this->translate('Email'),
        'show' => true,
      ],
      'language' => [
        'type' => 'varchar',
        'title' => $this->translate('Language'),
        'enumValues' => self::ENUM_LANGUAGES,
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


  public function prepareLoadRecordQuery(bool $addLookups = false): \Illuminate\Database\Eloquent\Builder {
    return parent::prepareLoadRecordQuery($addLookups)
      ->select('login', 'email', 'first_name', 'middle_name', 'last_name', 'is_active', 'language')
      ->with('ROLES')
    ;
  }

  public function getQueryForUser(int $idUser)
  {
    return $this->eloquent
      ->with('ROLES')
      ->with('PROFILE')
      ->where('id', $idUser)
      ->where('is_active', '<>', 0)
    ;
  }

  public function loadUser(int $idUser)
  {
    $user = $this->getQueryForUser($idUser)->first()?->toArray();

    $tmpRoles = [];
    foreach ($user['ROLES'] ?? [] as $role) {
      $tmpRoles[] = (int) $role['pivot']['id_role'];
    }
    $user['ROLES'] = $tmpRoles;

    return $user;
  }

  public function tableParams(array $params = []): array
  {
    $params = parent::tableParams();
    $params['title'] = 'Users';
    return $params;
  }

}