<?php

namespace HubletoApp\Community\Settings\Models;

use \ADIOS\Core\Db\Column\Varchar;
use \ADIOS\Core\Db\Column\Lookup;

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

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name'))),
      'middle_name' => (new Varchar($this, $this->translate('Middle name'))),
      'last_name' => (new Varchar($this, $this->translate('Last name'))),
      'email' => (new Varchar($this, $this->translate('Email'))),
      'language' => (new Varchar($this, $this->translate('Language')))->setEnumValues(self::ENUM_LANGUAGES),
      'id_active_profile' => (new Lookup($this, $this->translate("Active profile"), Profile::class)),
    ]);
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

  public function describeTable(): \ADIOS\Core\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = 'Users';
    $description->ui['addButtonText'] = 'Add User';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    return $description;
  }

}