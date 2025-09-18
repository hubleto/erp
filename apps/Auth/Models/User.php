<?php

namespace Hubleto\App\Community\Auth\Models;

use Hubleto\App\Community\Settings\Models\Company;
use Hubleto\App\Community\Auth\Models\UserHasRole;
use Hubleto\Erp\Model;
use Hubleto\Framework\Db\Column\Boolean;
use Hubleto\Framework\Db\Column\DateTime;
use Hubleto\Framework\Db\Column\Integer;
use Hubleto\Framework\Db\Column\Json;
use Hubleto\Framework\Db\Column\Lookup;
use Hubleto\Framework\Db\Column\Password;
use Hubleto\Framework\Db\Column\Varchar;

class User extends \Hubleto\Framework\Models\User
{

  public const int TYPE_NOT_SPECIFIED = 0;
  public const int TYPE_ADMINISTRATOR = 1;
  public const int TYPE_CHIEF_OFFICER = 2;
  public const int TYPE_MANAGER = 3;
  public const int TYPE_EMPLOYEE = 4;
  public const int TYPE_ASSISTANT = 5;
  public const int TYPE_EXTERNAL = 6;

  public const TYPE_ENUM_VALUES = [
    self::TYPE_NOT_SPECIFIED => 'NOT_SPECIFIED',
    self::TYPE_ADMINISTRATOR => 'ADMINISTRATOR',
    self::TYPE_CHIEF_OFFICER => 'Chief Officer (CEO, CFO, CTO, ...)',
    self::TYPE_MANAGER => 'Manager (Sales, Project, ...)',
    self::TYPE_EMPLOYEE => 'Employee',
    self::TYPE_ASSISTANT => 'Assistant',
    self::TYPE_EXTERNAL => 'External',
  ];

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

  public string $table = 'users';
  public string $recordManagerClass = RecordManagers\User::class;
  public ?string $lookupSqlValue = 'ifnull({%TABLE%}.nick, {%TABLE%}.email)';

  public string $translationContext = 'Hubleto\\App\\Community\\Settings\\Loader::Models\\User';
  public string $permission = 'Hubleto/App/Community/Settings/Loader::Models/User';
  public array $rolePermissions = [ ];

  public ?array $junctions = [
    'roles' => [
      'junctionModel' => UserHasRole::class,
      'masterKeyColumn' => 'id_user',
      'optionKeyColumn' => 'id_role',
    ],
    'tokens' => [
      'junctionModel' => UserHasToken::class,
      'masterKeyColumn' => 'id_user',
      'optionKeyColumn' => 'id_token',
    ],
  ];

  public function indexes(array $indexes = []): array
  {
    return parent::indexes([
      "login" => [
        "type" => "unique",
        "columns" => [
          "login" => [
            "order" => "asc",
          ],
        ],
      ],
    ]);
  }

  public function __construct()
  {
    parent::__construct();

    /** @var Token $tokenModel */
    $tokenModel = $this->getModel(Token::class);

    if (!$tokenModel->isTokenTypeRegistered(Token::TOKEN_TYPE_USER_FORGOT_PASSWORD)) {
      $tokenModel->registerTokenType(Token::TOKEN_TYPE_USER_FORGOT_PASSWORD);
    }

    if (!$tokenModel->isTokenTypeRegistered(Token::TOKEN_TYPE_USER_REMEMBER_ME)) {
      $tokenModel->registerTokenType(Token::TOKEN_TYPE_USER_REMEMBER_ME);
    }
  }

  public function describeColumns(): array
  {
    return array_merge(parent::describeColumns(), [
      'login' => new Varchar($this, 'Login'),
      'password' => new Password($this, 'Password'),
      'is_active' => new Boolean($this, 'Active'),
      'last_login_time' => new DateTime($this, 'Time of last login'),
      'last_login_ip' => new Varchar($this, 'Last login IP'),
      'last_access_time' => new DateTime($this, 'Time of last access'),
      'last_access_ip' => new Varchar($this, 'Last access IP'),
      'type' => (new Integer($this, $this->translate('Type')))->setEnumValues(self::TYPE_ENUM_VALUES),
      'first_name' => (new Varchar($this, $this->translate('First name'))),
      'last_name' => (new Varchar($this, $this->translate('Last name'))),
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

  public function getClientIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
  }

  public function updateAccessInformation(int $idUser): void
  {
    $clientIp = $this->getClientIpAddress();
    $this->record->where('id', $idUser)->update([
      'last_access_time' => date('Y-m-d H:i:s'),
      'last_access_ip' => $clientIp,
    ]);
  }

  public function updateLoginAndAccessInformation(int $idUser): void
  {
    $clientIp = $this->getClientIpAddress();
    $this->record->where('id', $idUser)->update([
      'last_login_time' => date('Y-m-d H:i:s'),
      'last_login_ip' => $clientIp,
      'last_access_time' => date('Y-m-d H:i:s'),
      'last_access_ip' => $clientIp,
    ]);
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

    unset($description->columns['password']);

    $description->ui['title'] = '';
    $description->ui['addButtonText'] = 'Add User';
    $description->ui['showHeader'] = true;
    $description->ui['showFulltextSearch'] = true;
    $description->ui['showFooter'] = false;

    $description->permissions['canDelete'] = false;

    $description->columns = [
      'type' => $description->columns['type'],
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

  public function isUserActive($user): bool {
    return $user['is_active'] == 1;
  }

  public function authCookieGetLogin(): string
  {
    list($tmpHash, $tmpLogin) = explode(",", $_COOKIE[$this->sessionManager()->getSalt() . '-user']);
    return $tmpLogin;
  }

  public function authCookieSerialize($login, $password): string
  {
    return md5($login.".".$password).",".$login;
  }

  public function generateToken($idUser, $tokenSalt, $tokenType): mixed
  {
    /** @var Token $tokenModel */
    $tokenModel = $this->getModel(Token::class);
    $token = $tokenModel->generateToken($tokenSalt, $tokenType);

    $this->record->updateRow([
      "id_token_reset_password" => $token['id'],
    ], $idUser);

    return $token['token'];
  }

  public function generatePasswordResetToken($idUser, $tokenSalt): mixed
  {
    return $this->generateToken(
      $idUser,
      $tokenSalt,
      self::TOKEN_TYPE_USER_FORGOT_PASSWORD
    );
  }

  public function validateToken($token, $deleteAfterValidation = TRUE): array
  {
    /** @var Token $tokenModel */
    $tokenModel = $this->getModel(Token::class);
    $tokenData = $tokenModel->validateToken($token);

    $userData = $this->record->where(
      'id_token_reset_password', $tokenData['id']
    )->first()
    ;

    if (!empty($userData)) {
      $userData = $userData->toArray();
    }

    if ($deleteAfterValidation) {
      $this->record->updateRow([
        "id_token_reset_password" => NULL,
      ], $userData["id"]);

      $tokenModel->deleteToken($tokenData['id']);
    }

    return $userData;
  }

  public function getByEmail(string $email) {
    $user = $this->record->where("email", $email)->first();

    return !empty($user) ? $user->toArray() : [];
  }

  public function encryptPassword(string $password): string {
    return password_hash($password, PASSWORD_DEFAULT);
  }

  public function updatePassword(int $idUser, string $password) {
    return $this->record
      ->where('id', $idUser)
      ->update(
        ["password" => $this->encryptPassword($password)]
      )
      ;
  }

}
