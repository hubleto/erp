<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\Token;
use Hubleto\Framework\Core;
use Hubleto\Framework\Model;

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
 * @property \Hubleto\Erp\Loader $main
 */
class AuthProvider extends Core implements Interfaces\AuthInterface
{

  public $loginAttribute = 'login';
  public $passwordAttribute = 'password';
  public $activeAttribute = 'is_active';
  public $verifyMethod = 'password_verify';

  public array $user = [];

  public function init(): void
  {
    $userLanguage = $this->getUserLanguage();
    if (empty($userLanguage)) $userLanguage = 'en';
    $this->config()->set('language', $userLanguage);
  }

  public function updateUserInSession(array $user): void
  {
    $this->sessionManager()->set('userProfile', $user);
  }

  public function isUserInSession(): bool
  {
    $user = $this->getUserFromSession();
    return isset($user['id']) && $user['id'] > 0;
  }

  function deleteSession()
  {
    $this->sessionManager()->clear();
    $this->user = [];

    setcookie($this->sessionManager()->getSalt() . '-user', '', 0);
    setcookie($this->sessionManager()->getSalt() . '-language', '', 0);
  }

  /**
   * Get information about authenticated user.
   *
   * @return UserProfile
   * 
   */
  public function getUser(): array
  {
    $tmp = $this->getUserFromSession();
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
    $tmp = $this->sessionManager()->get('userProfile') ?? [];
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
    $login = $this->router()->urlParamAsString('login');

    $mUser = $this->getService(User::class);
    if ($mUser->record->where('login', $login)->count() > 0) {
      $user = $mUser->record->where('login', $login)->first();

      $mToken = $this->getService(Token::class); // todo: token creation should be done within the token itself
      if (get_class($mToken) != Token::class) {
        throw new \Exception('Token model must be instance of \Hubleto\Framework\Models\Token class');
      }
      $tokenSalt = bin2hex(random_bytes(16));
      $token = $mToken->generateToken($tokenSalt, User::TOKEN_TYPE_USER_FORGOT_PASSWORD, date("Y-m-d H:i:s", strtotime("+ 30 minute", time())));

      if ($user['middle_name'] != '') {
        $name = $user['first_name'] . ' ' . $user['middle_name'] . ' '. $user['last_name'];
      } else {
        $name = $user['first_name'] . ' ' . $user['last_name'];
      }

      if ($user->password != '') {
//        $this->emailProvider()->sendResetPasswordEmail($login, $name, $user['language'] ?? 'en', $token['token']);
      } else {
//        $this->emailProvider()->sendWelcomeEmail($login, $name, $user['language'] ?? 'en', $token['token']);
      }
    }
  }

  public function resetPassword(): void
  {
    /** @var Token $mToken */
    $mToken = $this->getService(Token::class);
    /** @var User $mUser */
    $mUser = $this->getService(User::class);

    $token = $mToken->record->where('token', $this->router()->urlParamAsString('token'))->first();
    $user = $mUser->record->where('login', $token->login)->first();
    $oldPassword = $user->password;

    $user->update(['password' => password_hash($this->router()->urlParamAsString('password'), PASSWORD_DEFAULT)]);

    if ($oldPassword == "") {
      $this->router()->setUrlParam('login', $token->login);
      $token->delete();
      $this->router()->setUrlParam('password', $this->router()->urlParamAsString('password'));

      $this->getService(AuthProvider::class)->auth();
    } else {
      $token->delete();
    }
  }

  public function auth(): void
  {
    setcookie('incorrectLogin', '', time() - 3600);

    /** @var Models\User */
    $userModel = $this->createUserModel();

    if (!$this->isUserInSession()) {
      $login = $this->router()->urlParamAsString('login');
      $password = $this->router()->urlParamAsString('password');
      $rememberLogin = $this->router()->urlParamAsBool('session-persist');

      $login = trim($login);

      if (empty($login) && !empty($_COOKIE[$this->sessionManager()->getSalt() . '-user'])) {
        $login = $userModel->authCookieGetLogin();
      }

      if (!empty($login) && !empty($password)) {
        $users = $this->findUsersByLogin($login);

        foreach ($users as $user) {
          $passwordMatch = $this->verifyPassword($password, $user[$this->passwordAttribute]);

          if ($passwordMatch) {
            $authResult = $userModel->loadUser($user['id']);
            $this->signIn($authResult);

            if ($rememberLogin) {
              $this->sessionManager()->prolongSession();
            }

            break;

          }
        }
      }
    }

    $setLanguage = $this->router()->urlParamAsString('set-language');

    if (
      !empty($setLanguage)
      && !empty(\Hubleto\App\Community\Settings\Models\User::ENUM_LANGUAGES[$setLanguage])
    ) {
      $mUser = $this->getModel(\Hubleto\App\Community\Settings\Models\User::class);
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
      $this->router()->redirectTo('');
    }

    if (strlen($this->getUserLanguage()) !== 2) {
      $this->setUserLanguage('en');
    }
  }

  public function signIn(array $user)
  {
    $this->user = $user;
    $this->updateUserInSession($user);
  }

  public function signOut()
  {
    $this->deleteSession();
    $this->router()->redirectTo('?signed-out');
    exit;
  }

  public function createUserModel(): Model
  {
    return $this->getModel(Models\User::class);
  }

  public function verifyPassword($password1, $password2): bool
  {
    return password_verify($password1, $password2);
  }

  public function getActiveUsers(): array
  {
    return (array) $this->createUserModel()->record
      ->where($this->activeAttribute, '<>', 0)
      ->get()
      ->toArray()
      ;
  }

  public function getUserType(): int
  {
    $user = $this->getUser();
    return $user['type'] ?? 0;
  }

  public function getUserRoles(): array
  {
    $user = $this->getUser();
    if (isset($user['ROLES']) && is_array($user['ROLES'])) return $user['ROLES'];
    else if (isset($user['roles']) && is_array($user['roles'])) return $user['roles'];
    else return [];
  }

  public function userHasRole(int $idRole): bool
  {
    return in_array($idRole, $this->getUserRoles());
  }

  public function getUserId(): int
  {
    return (int) ($this->getUser()['id'] ?? 0);
  }

  public function getUserEmail(): string
  {
    return (string) ($this->getUser()['email'] ?? '');
  }

  public function getUserLanguage(): string
  {
    $user = $this->getUserFromSession() ?? [];
    if (isset($user['language']) && strlen($user['language']) == 2) {
      return $user['language'];
    } else if (isset($_COOKIE['language']) && strlen($_COOKIE['language']) == 2) {
      return $_COOKIE['language'];
    } else {
      $language = $this->config()->getAsString('language', 'en');
      if (strlen($language) !== 2) $language = 'en';
      return $language;
    }
  }
  public function setUserLanguage(string $language): void {
    $user = $this->getUser();
    $user['language'] = $language;
    $this->updateUserInSession($user);
  }

}
