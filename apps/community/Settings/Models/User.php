<?php

namespace HubletoApp\Community\Settings\Models;

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
  public ?string $lookupSqlValue = '{%TABLE%}.email';

  public function columnsLegacy(array $columns = []): array
  {
    return parent::columnsLegacy(array_merge($columns, [
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
        'model' => Profile::class,
        'title' => $this->translate('Active profile'),
        'show' => true,
      ],
    ]));
  }

  public function prepareLoadRecordQuery(array $includeRelations = [], int $maxRelationLevel = 0, mixed $query = null, int $level = 0): mixed
  {
    return parent::prepareLoadRecordQuery($includeRelations, $maxRelationLevel, $query, $level)
      ->with('ROLES')
    ;
  }

  public function getQueryForUser(int $idUser): mixed
  {
    return $this->eloquent
      ->with('ROLES')
      ->with('PROFILE')
      ->where('id', $idUser)
      ->where('is_active', '<>', 0)
    ;
  }

  public function loadUser(int $idUser): array
  {
    $user = (array) $this->getQueryForUser($idUser)->first()?->toArray();

    $tmpRoles = [];
    if (is_array($user['ROLES'])) {
      foreach ($user['ROLES'] as $role) {
        $tmpRoles[] = (int) $role['pivot']['id_role']; // @phpstan-ignore-line
      }
    }
    $user['ROLES'] = $tmpRoles;

    return $user;
  }

  public function tableDescribe(array $description = []): array
  {
    $description = parent::tableDescribe($description);

    if (is_array($description['ui'])) {
      $description['ui']['title'] = 'Users';
      $description['ui']['addButtonText'] = 'Add User';
      $description['ui']['showHeader'] = true;
      $description['ui']['showFooter'] = false;
    }

    return $description;
  }

}