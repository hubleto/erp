<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth;


use Hubleto\App\Community\Auth\Models\Token;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\UserHasToken;
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
class AuthProvider extends \Hubleto\Framework\AuthProvider
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

      /** @var Token $mToken */
      $mToken = $this->getService(Token::class);
      $tokenSalt = bin2hex(random_bytes(16));
      $token = $mToken->generateToken($tokenSalt, Token::TOKEN_TYPE_USER_FORGOT_PASSWORD, date("Y-m-d H:i:s", strtotime("+ 30 minute", time())));

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

      $this->getService(\Hubleto\Framework\AuthProvider::class)->auth();
    } else {
      $token->delete();
    }
  }

  private function initiateRememberMe($userId) {

    /** @var Token $mToken */
    $mToken = $this->getService(Token::class);
    $token = $mToken->generateToken($this->config()->getAsString('sessionSalt'), Token::TOKEN_TYPE_USER_REMEMBER_ME, date("Y-m-d H:i:s", strtotime("+ 30 day", time())));

    $mJunctionTable = $this->getModel(UserHasToken::class);
    $mJunctionTable->record->recordCreate([
      'id_user' => $userId,
      'id_token' => $token['id'],
    ]);

    setcookie($this->config()->getAsString('accountUid') . '-rememberMe', $token['token'], time() + (86400 * 30), "/");
  }

  public function authenticateRememberedUser(): bool {

    /** @var Token $mToken */
    $mToken = $this->getService(Token::class);
    $matchingTokensQuery = $mToken
      ->record
      ->where(
        'token',
        $_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe'] ?? ''
      )->where('type', Token::TOKEN_TYPE_USER_REMEMBER_ME)
      ->where('valid_to', '>', date("Y-m-d H:i:s"));
    if (
      $matchingTokensQuery->count() > 0)
    {
      $tokenId = $matchingTokensQuery->first()->id;

      $mJunctionTable = $this->getModel(UserHasToken::class);
      $junctionEntry = $mJunctionTable->record->where('id_token', $tokenId)->first();
      if (!empty($junctionEntry)) {
        $userId = $junctionEntry['id_user'];
        /** @var User $userModel */
        $userModel = $this->getService(User::class);

        $authResult = $userModel->loadUser($userId);
        $this->signIn($authResult);
        return true;
      }
    }

    return false;
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
              $this->initiateRememberMe($user['id']);
            }

            break;

          }
        }
      }
    }

    $setLanguage = $this->router()->urlParamAsString('set-language');

    if (
      !empty($setLanguage)
      && !empty(\Hubleto\App\Community\Auth\Models\User::ENUM_LANGUAGES[$setLanguage])
    ) {
      $mUser = $this->getModel(\Hubleto\App\Community\Auth\Models\User::class);
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

  public function createUserModel(): Model
  {
    return $this->getModel(Models\User::class);
  }

  public function userHasRole(int $idRole): bool
  {
    return in_array($idRole, $this->getUserRoles());
  }

  public function signOut()
  {
    if (isset($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe'])) {
      unset($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe']);
      setcookie($this->config()->getAsString('accountUid') . '-rememberMe', '', time() - 3600, "/");
    }

    /** @var Token $mToken */
    $mToken = $this->getService(Token::class);
    /** @var UserHasToken $mJunctionTable */
    $mJunctionTable = $this->getService(UserHasToken::class);

    $tokenIds = $mJunctionTable->record->where('id_user', $this->getUserId())->get(['id_token']);
    $mJunctionTable->record->where('id_user', $this->getUserId())->delete();

    foreach ($tokenIds as $entry) {
      $mToken->record->where('id', $entry['id_token'])->delete();
    }

    parent::signOut();
  }

}
