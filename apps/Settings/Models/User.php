<?php

namespace HubletoApp\Community\Settings\Models;

use Hubleto\Framework\Db\Column\Varchar;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Json;

class User extends \Hubleto\Framework\Models\User
{
  public const ENUM_LANGUAGES = [
    'en' => 'English',
    'de' => 'Deutsch',
    'fr' => 'Francais',
    'es' => 'Español',
    'sk' => 'Slovensky',
    'cs' => 'Česky',
    'pl' => 'Polski',
    'ro' => 'Română',
  ];

  /**
   * @var \Illuminate\Database\Eloquent\Model
   */
  public object $record;

  public string $table = 'users';
  public string $recordManagerClass = RecordManagers\User::class;
  public ?string $lookupSqlValue = 'ifnull({%TABLE%}.nick, {%TABLE%}.email)';

  public string $translationContext = 'HubletoApp\\Community\\Settings\\Loader::Models\\User';
  public string $permission = 'HubletoApp/Community/Settings/Loader::Models/User';
  public array $rolePermissions = [ ];

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'first_name' => (new Varchar($this, $this->translate('First name')))->setRequired(),
      'last_name' => (new Varchar($this, $this->translate('Last name')))->setRequired(),
      'nick' => (new Varchar($this, $this->translate('Nick'))),
      'email' => (new Varchar($this, $this->translate('Email')))->setRequired(),
      'language' => (new Varchar($this, $this->translate('Language')))->setEnumValues(self::ENUM_LANGUAGES)->setRequired(),
      'id_default_company' => (new Lookup($this, $this->translate("Default company"), Company::class)),
      'apps' => (new Json($this, $this->translate('Apps'))),
    ]);
  }

  public function getQueryForUser(int $idUser): mixed
  {
    return $this->record
      ->with('ROLES')
      ->with('TEAMS')
      ->with('DEFAULT_COMPANY')
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

  public function describeTable(): \Hubleto\Framework\Description\Table
  {
    $description = parent::describeTable();

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Add User';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    $description->permissions['canDelete'] = false;

    $description->columns = [
      'first_name' => $description->columns['first_name'],
      'last_name' => $description->columns['last_name'],
      'nick' => $description->columns['nick'],
      'email' => $description->columns['email'],
      'language' => $description->columns['language'],
      'id_default_company' => $description->columns['id_default_company'],
      'is_active' => $description->columns['is_active'],
      'roles' => (new Varchar($this, $this->translate('Roles'))),
      'teams' => (new Varchar($this, $this->translate('Teams'))),
    ];

    return $description;
  }

  public function describeForm(): \Hubleto\Framework\Description\Form
  {
    $description = parent::describeForm();
    // $description->permissions = [
    //   'canDelete' => false,
    //   'canUpdate' => true,
    // ];
    return $description;
  }

}
