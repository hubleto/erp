<?php

namespace HubletoMain;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Models\Token;

/**
 * Default authentication provider class.
 *
 * @phpstan-type UserProfile array{
 *   id: int,
 *   first_name: string,
 *   last_name: string,
 *   login: string,
 *   email: string,
 *   language: string,
 *   ROLES: array<mixed>,
 *   TEAMS: array<mixed>,
 *   DEFAULT_COMPANY: array<mixed>,
 * }
 */
class AuthProvider extends \Hubleto\Framework\Auth\DefaultProvider
{

  use \Hubleto\Framework\Traits\MainTrait;

  /**
   * Class constructor.
   *
   * @param \Hubleto\Framework\Loader $main
   * 
   */
  public function __construct(\Hubleto\Framework\Loader $main)
  {
    parent::__construct($main);
    $this->main = $main;

    $this->main->registerModel(\HubletoApp\Community\Settings\Models\User::class);
    $this->main->registerModel(\HubletoApp\Community\Settings\Models\UserRole::class);
    $this->main->registerModel(\HubletoApp\Community\Settings\Models\UserHasRole::class);
  }

  /**
   * Get information about authenticated user.
   *
   * @return UserProfile
   * 
   */
  public function getUser(): array
  {
    $tmp = is_array($this->user) ? $this->user : [];
    return [
      'id' => (int) ($tmp['id'] ?? 0),
      'login' => (string) ($tmp['login'] ?? ''),
      'email' => (string) ($tmp['email'] ?? ''),
      'first_name' => (string) ($tmp['first_name'] ?? ''),
      'last_name' => (string) ($tmp['last_name'] ?? ''),
      'is_active' => (bool) ($tmp['is_active'] ?? false),
      'language' => (string) ($tmp['language'] ?? false),
      'ROLES' => (array) ($tmp['ROLES'] ?? []),
      'TEAMS' => (array) ($tmp['TEAMS'] ?? []),
      'DEFAULT_COMPANY' => (array) ($tmp['DEFAULT_COMPANY'] ?? []),
    ];
  }

  /**
   * Get user information from the session.
   *
   * @return UserProfile
   * 
   */
  public function getUserFromSession(): array
  {
    $tmp = $this->main->session->get('userProfile') ?? [];
    return [
      'id' => (int) ($tmp['id'] ?? 0),
      'login' => (string) ($tmp['login'] ?? ''),
      'email' => (string) ($tmp['email'] ?? ''),
      'first_name' => (string) ($tmp['first_name'] ?? ''),
      'last_name' => (string) ($tmp['last_name'] ?? ''),
      'is_active' => (bool) ($tmp['is_active'] ?? false),
      'language' => (string) ($tmp['language'] ?? false),
      'ROLES' => (array) ($tmp['ROLES'] ?? []),
      'TEAMS' => (array) ($tmp['TEAMS'] ?? []),
      'DEFAULT_COMPANY' => (array) ($tmp['DEFAULT_COMPANY'] ?? []),
    ];
  }

  public function isUserMemberOfTeam(int $idTeam): bool
  {
    $user = $this->getUser();
    foreach ($user['TEAMS'] as $team) {
      if ($team['id'] ?? 0 == $idTeam) {
        return true;
      }
    }
    return false;
  }

  public function getActiveUsers(): array
  {
    return (array) $this->createUserModel()->record
      ->where($this->activeAttribute, '<>', 0)
      ->get()
      ->toArray()
    ;
  }

  public function createUserModel(): \HubletoApp\Community\Settings\Models\User
  {
    return $this->main->di->create(\HubletoApp\Community\Settings\Models\User::class);
  }

  public function findUsersByLogin(string $login): array
  {
    return $this->createUserModel()->record
      ->where('email', $login)
      ->where($this->activeAttribute, '<>', 0)
      ->get()
      ->makeVisible([$this->passwordAttribute])
      ->toArray()
    ;
  }

  public function forgotPassword(): void
  {
    $login = $this->main->urlParamAsString('login');

    $mUser = $this->main->di->create(User::class);
    if ($mUser->record->where('login', $login)->count() > 0) {
      $user = $mUser->record->where('login', $login)->first();

      $mToken = $this->main->di->create(Token::class); // todo: token creation should be done withing the token itself
      $tokenValue = bin2hex(random_bytes(16));
      $mToken->record->where('login', $login)->where('type', 'reset-password')->delete();
      $mToken->record->create([
        'login' => $login,
        'token' => $tokenValue,
        'valid_to' => $user->password != '' ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : date('Y-m-d H:i:s', strtotime('+14 days')),
        'type' => 'reset-password'
      ]);

      if ($user['middle_name'] != '') {
        $name = $user['first_name'] . ' ' . $user['middle_name'] . ' '. $user['last_name'];
      } else {
        $name = $user['first_name'] . ' ' . $user['last_name'];
      }

      if ($user->password != '') {
        $this->main->emails->sendResetPasswordEmail($login, $name, $user['language'] ?? 'en', $tokenValue);
      } else {
        $this->main->emails->sendWelcomeEmail($login, $name, $user['language'] ?? 'en', $tokenValue);
      }
    }
  }

  public function resetPassword(): void
  {
    $mToken = $this->main->di->create(Token::class);
    $mUser = $this->main->di->create(User::class);

    $token = $mToken->record->where('token', $this->main->urlParamAsString('token'))->first();
    $user = $mUser->record->where('login', $token->login)->first();
    $oldPassword = $user->password;

    $user->update(['password' => password_hash($this->main->urlParamAsString('password'), PASSWORD_DEFAULT)]);

    if ($oldPassword == "") {
      $this->main->setUrlParam('login', $token->login);
      $token->delete();
      $this->main->setUrlParam('password', $this->main->urlParamAsString('password'));

      $this->main->auth->auth(false);
    } else {
      $token->delete();
    }
  }

  public function auth(): void
  {
    setcookie('incorrectLogin', '', time() - 3600);

    parent::auth();

    $setLanguage = $this->main->urlParamAsString('set-language');

    if (
      !empty($setLanguage)
      && !empty(\HubletoApp\Community\Settings\Models\User::ENUM_LANGUAGES[$setLanguage])
    ) {
      $mUser = $this->main->di->create(\HubletoApp\Community\Settings\Models\User::class);
      $mUser->record
        ->where('id', $this->getUserId())
        ->update(['language' => $setLanguage])
      ;
      $this->user['language'] = $setLanguage;

      if ($this->isUserInSession()) {
        $this->updateUserInSession($this->user);
      }

      $date = date("D, d M Y H:i:s", strtotime('+1 year')) . 'GMT';
      header("Set-Cookie: language={$setLanguage}; EXPIRES{$date};");
      setcookie('incorrectLogin', '1');
      $this->main->router->redirectTo('');
    }

    if (strlen((string) ($this->user['language'] ?? '')) != 2) {
      $this->user['language'] = 'en';
    }
  }

}
