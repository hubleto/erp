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
  public ?string $lookupSqlValue = '{%TABLE%}.email';
  public string $translationContext = 'mod.core.settings.models.user';

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
        'model' => Profile::class,
        'title' => $this->translate('Active profile'),
        'show' => true,
      ],
    ]));
  }

  public function prepareLoadRecordQuery(array|null $includeRelations = null, int $maxRelationLevel = 0, $query = null, int $level = 0)
  {
    return parent::prepareLoadRecordQuery($includeRelations, $maxRelationLevel, $query, $level)
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

  public function tableDescribe(array $description = []): array
  {
    $description["model"] = $this->fullName;
    $description = parent::tableDescribe($description);
    $description['ui']['title'] = 'Users';
    $description['ui']['addButtonText'] = 'Add User';
    $description['ui']['showHeader'] = true;
    $description['ui']['showFooter'] = false;
    return $description;
  }

}