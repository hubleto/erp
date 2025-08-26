<?php declare(strict_types=1);

namespace HubletoMain;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Models\Token;

/**
 * Default authentication provider class.
 *
 * @phpstan-type UserProfile array{
 *   id: int,
 *   type: int,
 *   first_name: string,
 *   last_name: string,
 *   login: string,
 *   email: string,
 *   language: string,
 *   apps: string,
 *   ROLES: array<mixed>,
 *   TEAMS: array<mixed>,
 *   DEFAULT_COMPANY: array<mixed>,
 * }
 *
 * @property \HubletoMain\Loader $main
 */
class AuthProvider extends \Hubleto\Framework\Auth\DefaultProvider
{

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
      'type' => (int) ($tmp['type'] ?? 0),
      'login' => (string) ($tmp['login'] ?? ''),
      'email' => (string) ($tmp['email'] ?? ''),
      'first_name' => (string) ($tmp['first_name'] ?? ''),
      'last_name' => (string) ($tmp['last_name'] ?? ''),
      'is_active' => (bool) ($tmp['is_active'] ?? false),
      'language' => (string) ($tmp['language'] ?? false),
      'apps' => (string) ($tmp['apps'] ?? ''),
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
      'type' => (int) ($tmp['type'] ?? 0),
      'login' => (string) ($tmp['login'] ?? ''),
      'email' => (string) ($tmp['email'] ?? ''),
      'first_name' => (string) ($tmp['first_name'] ?? ''),
      'last_name' => (string) ($tmp['last_name'] ?? ''),
      'is_active' => (bool) ($tmp['is_active'] ?? false),
      'language' => (string) ($tmp['language'] ?? false),
      'apps' => (string) ($tmp['apps'] ?? ''),
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

  public function createUserModel(): \HubletoApp\Community\Settings\Models\User
  {
    return $this->main->load(\HubletoApp\Community\Settings\Models\User::class);
  }

  public function findUsersByLogin(string $login): array
  {
    return $this->createUserModel()->record
      ->where('email', trim($login))
      ->where($this->activeAttribute, '<>', 0)
      ->get()
      ->makeVisible([$this->passwordAttribute])
      ->toArray()
    ;
  }

  public function forgotPassword(): void
  {
    $login = $this->main->urlParamAsString('login');

    $mUser = $this->main->load(User::class);
    if ($mUser->record->where('login', $login)->count() > 0) {
      $user = $mUser->record->where('login', $login)->first();

      $mToken = $this->main->load(Token::class); // todo: token creation should be done withing the token itself
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
    $mToken = $this->main->load(Token::class);
    $mUser = $this->main->load(User::class);

    $token = $mToken->record->where('token', $this->main->urlParamAsString('token'))->first();
    $user = $mUser->record->where('login', $token->login)->first();
    $oldPassword = $user->password;

    $user->update(['password' => password_hash($this->main->urlParamAsString('password'), PASSWORD_DEFAULT)]);

    if ($oldPassword == "") {
      $this->main->setUrlParam('login', $token->login);
      $token->delete();
      $this->main->setUrlParam('password', $this->main->urlParamAsString('password'));

      $this->main->auth->auth();
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
      $mUser = $this->main->load(\HubletoApp\Community\Settings\Models\User::class);
      $mUser->record
        ->where('id', $this->getUserId())
        ->update(['language' => $setLanguage])
      ;
      $this->setUserLanguage($setLanguage);

      if ($this->isUserInSession()) {
        $this->updateUserInSession($this->user);
      }

      $date = date("D, d M Y H:i:s", strtotime('+1 year')) . 'GMT';
      header("Set-Cookie: language={$setLanguage}; EXPIRES{$date};");
      setcookie('incorrectLogin', '1');
      $this->getRouter()->redirectTo('');
    }

    if (strlen($this->getUserLanguage()) !== 2) {
      $this->setUserLanguage('en');
    }
  }

}
