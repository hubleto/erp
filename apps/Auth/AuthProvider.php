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
    $apps = @json_decode($tmp['apps'] ?? '');
    if (!is_array($apps)) $apps = [];

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
      'APPS' => (array) $apps,
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

  /**
   * [Description for findUsersByLogin]
   *
   * @param string $login
   *
   * @return array
   *
   */
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

  /**
   * [Description for forgotPassword]
   *
   * @return void
   *
   */
  public function forgotPassword(): void
  {
    $login = $this->router()->urlParamAsString('login');

    $mUser = $this->getModel(User::class);
    if ($mUser->record->where('email', $login)->count() > 0) {
      $user = $mUser->record->where('email', $login)->first();

      /** @var Token $mToken */
      $mToken = $this->getModel(Token::class);
      $tokenSalt = bin2hex(random_bytes(16));
      $token = $mToken->generateToken($tokenSalt, Token::TOKEN_TYPE_USER_FORGOT_PASSWORD, date("Y-m-d H:i:s", strtotime("+ 30 minute", time())));

      if ($user['middle_name'] != '') {
        $name = $user['first_name'] . ' ' . $user['middle_name'] . ' '. $user['last_name'];
      } else {
        $name = $user['first_name'] . ' ' . $user['last_name'];
      }

      $mJunctionTable = $this->getModel(UserHasToken::class);
      $mJunctionTable->record->recordCreate([
        'id_user' => $user['id'],
        'id_token' => $token['id'],
      ]);

      if ($user->password != '') {
        $this->emailProvider()->sendResetPasswordEmail($login, $name, $user['language'] ?? 'en', $token['token']);
      } else {
        $this->emailProvider()->sendWelcomeEmail($login, $name, $user['language'] ?? 'en', $token['token']);
      }
    }
  }

  /**
   * [Description for resetPassword]
   *
   * @return void
   *
   */
  public function resetPassword(): void
  {
    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);

    /** @var User $mUser */
    $mUser = $this->getModel(User::class);

    /** @var UserHasToken $mJunctionTable */
    $mJunctionTable = $this->getModel(UserHasToken::class);

    $token = $mToken->record->where('token', $this->router()->urlParamAsString('token'))->first();
    $junctionEntry = $mJunctionTable->record->where('id_token', $token->id)->first();
    $user = $mUser->record->where('id', $junctionEntry->id_user)->first();
    $oldPassword = $user->password;

    $user->update(['password' => password_hash($this->router()->urlParamAsString('password'), PASSWORD_DEFAULT)]);

    $junctionEntry->delete();
    $token->delete();

    if ($oldPassword == "") {
      $this->router()->setUrlParam('login', $user->email);
      $this->router()->setUrlParam('password', $this->router()->urlParamAsString('password'));

      $this->getService(\Hubleto\Framework\AuthProvider::class)->auth();
    } else {
      setcookie('passwordReset', '1', time() + 1000, "/");
    }
  }

  /**
   * [Description for initiateRememberMe]
   *
   * @param mixed $userId
   *
   * @return [type]
   *
   */
  private function initiateRememberMe($userId) {

    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);
    $token = $mToken->generateToken($this->config()->getAsString('sessionSalt'), Token::TOKEN_TYPE_USER_REMEMBER_ME, date("Y-m-d H:i:s", strtotime("+ 30 day", time())));

    $mJunctionTable = $this->getModel(UserHasToken::class);
    $mJunctionTable->record->recordCreate([
      'id_user' => $userId,
      'id_token' => $token['id'],
    ]);

    setcookie($this->config()->getAsString('accountUid') . '-rememberMe', $token['token'], time() + (86400 * 30), "/");
  }

  /**
   * [Description for authenticateRememberedUser]
   *
   * @return bool
   *
   */
  public function authenticateRememberedUser(): bool {

    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);
    $tokenId = $mToken->validateToken($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe'] ?? '', Token::TOKEN_TYPE_USER_REMEMBER_ME);
    if (
      $tokenId !== false)
    {
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

  /**
   * [Description for auth]
   *
   * @return void
   *
   */
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

        $successful = false;

        foreach ($users as $user) {
          $passwordMatch = $this->verifyPassword($password, $user[$this->passwordAttribute]);

          if ($passwordMatch) {
            $authResult = $userModel->loadUser($user['id']);
            $this->signIn($authResult);

            $successful = true;

            if ($rememberLogin) {
              $this->initiateRememberMe($user['id']);
            }

            break;

          }
        }

        if (!$successful) {
          setcookie('incorrectLogin', "1", time(), "/");
        }
      }
    }

  }

  /**
   * [Description for createUserModel]
   *
   * @return Model
   *
   */
  public function createUserModel(): Model
  {
    return $this->getModel(Models\User::class);
  }

  /**
   * [Description for userHasRole]
   *
   * @param int $idRole
   *
   * @return bool
   *
   */
  public function userHasRole(int $idRole): bool
  {
    return in_array($idRole, $this->getUserRoles());
  }

  /**
   * [Description for signOut]
   *
   * @return [type]
   *
   */
  public function signOut()
  {
    if (isset($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe'])) {
      unset($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe']);
      setcookie($this->config()->getAsString('accountUid') . '-rememberMe', '', time() - 3600, "/");
    }

    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);
    /** @var UserHasToken $mJunctionTable */
    $mJunctionTable = $this->getModel(UserHasToken::class);

    $tokenIds = $mJunctionTable->record->where('id_user', $this->getUserId())->get(['id', 'id_token']);

    foreach ($tokenIds as $entry) {
      if ($mToken->record->where('id', $entry['id_token'])->where('type', Token::TOKEN_TYPE_USER_REMEMBER_ME)->count() > 0) {
        $mJunctionTable->record->where('id', $entry['id'])->delete();
      }
      $mToken->record->where('id', $entry['id_token'])->where('type', Token::TOKEN_TYPE_USER_REMEMBER_ME)->delete();
    }

    parent::signOut();
  }

}
