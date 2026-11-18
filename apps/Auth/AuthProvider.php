<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth;


use Hubleto\App\Community\Auth\Models\Token;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\UserHasToken;
use Hubleto\Erp\Core;
use Hubleto\Erp\EmailProvider;
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
 *   force_signout: bool,
 *   apps: string,
 *   ROLES: array<mixed>,
 *   TEAMS: array<mixed>,
 *   DEFAULT_COMPANY: array<mixed>,
 * }
 *
 * @property \Hubleto\Erp\Loader $main
 */
class AuthProvider extends \Hubleto\Framework\Services\AuthProvider
{

  public $loginAttribute = 'login';
  public $passwordAttribute = 'password';
  public $activeAttribute = 'is_active';
  public $verifyMethod = 'password_verify';

  public array $user = [];

  /** Name of the session variable holding the state of an unfinished (second step pending) sign-in. */
  private const string SESSION_KEY_PENDING_2FA = 'pending2fa';

  /**
   * Result of the last two-factor operation (startTwoFactor, verifyTwoFactorCode, resendTwoFactorCode).
   * Empty string, or one of: codeSent, codeResent, invalidCode, codeExpired, tooManyAttempts, sendFailed, resendTooSoon.
   */
  public string $twoFactorStatus = '';

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
    return $this->normalizeUserProfile($this->getUserFromSession());
  }

  /**
   * [Description for normalizeUserProfile]
   *
   * @param array $user
   * 
   * @return array
   * 
   */
  public function normalizeUserProfile(array $user): array
  {
    $apps = @json_decode($user['apps'] ?? '');
    if (!is_array($apps)) $apps = [];

    return [
      'id' => (int) ($user['id'] ?? 0),
      'type' => (int) ($user['type'] ?? 0),
      'login' => (string) ($user['login'] ?? ''),
      'email' => (string) ($user['email'] ?? ''),
      'first_name' => (string) ($user['first_name'] ?? ''),
      'last_name' => (string) ($user['last_name'] ?? ''),
      'is_active' => (bool) ($user['is_active'] ?? false),
      'force_signout' => (bool) ($user['force_signout'] ?? false),
      'language' => (string) ($user['language'] ?? false),
      'timezone' => (string) ($user['timezone'] ?? false),
      'position' => (string) ($user['position'] ?? false),
      'photo' => (string) ($user['photo'] ?? false),
      'permissions' => (string) ($user['permissions'] ?? false),
      'apps' => (string) ($user['apps'] ?? ''),
      'password_last_change' => (string) ($user['password_last_change'] ?? ''),
      'APPS' => (array) $apps,
      'ROLES' => (array) ($user['ROLES'] ?? []),
      'TEAMS' => (array) ($user['TEAMS'] ?? []),
      'DEFAULT_COMPANY' => (array) ($user['DEFAULT_COMPANY'] ?? []),
    ];
  }

  /**
   * [Description for getUserFromDatabase]
   *
   * @return array
   * 
   */
  public function getUserFromDatabase(): array
  {
    $mUser = $this->createUserModel();

    $user = $mUser->record
      ->where($mUser->table . '.id', $this->getUserId())
      ->with('ROLES')
      ->with('TEAMS')
      ->with('DEFAULT_COMPANY')
      ->first()
      ->toArray();

    if ($user['force_signout']) {
      $mUser->record->where($mUser->table . '.id', $user['id'])->update(['force_signout' => 0]);
      $this->signOut();
      return [];
    }

    return $this->normalizeUserProfile($user);
  }

  /**
   * [Description for getUserPermissions]
   *
   * @return array
   * 
   */
  public function getUserPermissions(): array
  {
    $tmp = $this->getUserFromDatabase();
    $userPermissions = @json_decode($tmp['permissions'] ?? '', true);
    if (!is_array($userPermissions)) $userPermissions = [];

    $role = $tmp['ROLES'][0] ?? null;
    if ($role !== null) {
      $rolePermissions = @json_decode($role['permissions'] ?? '', true);
    } else {
      $rolePermissions = [];
    }

    $userPermissions['recordsRead'] = $userPermissions['recordsRead'] ?? ($rolePermissions['recordsRead'] ?? 'all');
    $userPermissions['recordsModify'] = $userPermissions['recordsModify'] ?? ($rolePermissions['recordsModify'] ?? 'all');

    return $userPermissions;
  }

  /**
   * [Description for isTeamMember]
   *
   * @param int $idTeam
   * 
   * @return bool
   * 
   */
  public function isTeamMember(int $idTeam): bool
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

      /** @var UserHasToken */
      $mUserHasToken = $this->getModel(UserHasToken::class);
      $mUserHasToken->record->recordCreate([
        'id_user' => $user['id'],
        'id_token' => $token['id'],
      ]);

      /** @var EmailProvider */
      $emailProvider = $this->getService(EmailProvider::class);

      if ($user->password != '') {
        $emailProvider->sendResetPasswordEmail($login, $name, $user['language'] ?? 'en', $token['token']);
      } else {
        $emailProvider->sendWelcomeEmail($login, $name, $user['language'] ?? 'en', $token['token']);
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

    /** @var UserHasToken */
    $mUserHasToken = $this->getModel(UserHasToken::class);

    $token = $mToken->record->where('token', $this->router()->urlParamAsString('token'))->first();
    $userHasToken = $mUserHasToken->record->where('id_token', $token->id)->first();
    $user = $mUser->record->where('id', $userHasToken->id_user)->first();
    $oldPassword = $user->password;

    $mUser->updatePassword($user['id'], $this->router()->urlParamAsString('password'));
    // $user->update(['password' => password_hash($this->router()->urlParamAsString('password'), PASSWORD_DEFAULT)]);

    $userHasToken->delete();
    $token->delete();

    if ($oldPassword == "") {
      $this->router()->setUrlParam('login', $token->login);
      $this->router()->setUrlParam('password', $this->router()->urlParamAsString('password'));

      $this->authProvider()->auth();
    } else {
      setcookie('passwordReset', '1', time() + 1000, "/");
    }
  }

  /**
   * [Description for initiateRememberMe]
   *
   * @return void
   * 
   */
  public function initiateRememberMe(): void {

    /** @var Token */
    $mToken = $this->getModel(Token::class);
    $token = $mToken->generateToken($this->config()->getAsString('sessionSalt'), Token::TOKEN_TYPE_USER_REMEMBER_ME, date("Y-m-d H:i:s", strtotime("+ 30 day", time())));

    /** @var UserHasToken */
    $mUserHasToken = $this->getModel(UserHasToken::class);
    $mUserHasToken->record->recordCreate([
      'id_user' => $this->getUserId(),
      'id_token' => $token['id'],
    ]);

    setcookie($this->config()->getAsString('accountUid') . '-rememberMe', $token['token'], time() + (86400 * 30), "/");
  }

  /**
   * Signs in the user using the 'remember me' cookie.
   *
   * By design, this does NOT require the second authentication step (code from email):
   * the 'remember me' cookie is issued only after the user has passed all authentication
   * steps (see verifyTwoFactorCode()) and it acts as a trusted-device token.
   *
   * @return bool
   * 
   */
  public function authenticateRememberedUser(): bool {

    /** @var Token */
    $mToken = $this->getModel(Token::class);
    $tokenId = $mToken->validateToken(
      $_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe'] ?? '',
      Token::TOKEN_TYPE_USER_REMEMBER_ME
    );

    if ($tokenId !== false) {

      /** @var UserHasToken */
      $mUserHasToken = $this->getModel(UserHasToken::class);
      $userHasToken = $mUserHasToken->record->where('id_token', $tokenId)->first();

      if (!empty($userHasToken)) {
        $userId = $userHasToken['id_user'];

        /** @var User */
        $userModel = $this->getModel(User::class);

        $this->logger()->info("Authenticating user {$userId} as 'rememberMe'.");

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

      if (empty($login)) {
        $login = $userModel->authCookieGetLogin();
      }

      if (!empty($login) && !empty($password)) {
        // a new sign-in attempt always replaces a previously started second step
        $this->clearPendingTwoFactor();
        $this->twoFactorStatus = '';

        $users = $userModel->findUsersByLogin($login);

        $successful = false;

        foreach ($users as $user) {
          $passwordMatch = $userModel->verifyPassword($user, $password);

          if ($passwordMatch) {
            $successful = true;

            if ($this->isTwoFactorEnabled()) {
              // first factor (password) is OK, the user is NOT signed in until the code from email is verified
              $this->startTwoFactor($user, $rememberLogin);
            } else {
              $authResult = $userModel->loadUser($user['id']);
              $this->signIn($authResult);

              if ($rememberLogin) {
                $this->initiateRememberMe();
              }
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
   * Signs in the user and discards any unfinished second step.
   *
   * @param array $user
   *
   * @return void
   *
   */
  public function signIn(array $user)
  {
    parent::signIn($user);
    $this->clearPendingTwoFactor();
  }

  // ---------------------------------------------------------------------------------------------
  // Second authentication step: 6-digit code sent to user's email
  //
  // Configuration (all optional):
  //   twoFactorEnabled          bool  Enables/disables the second step. If not set, it is enabled
  //                                   only when SMTP is configured (otherwise nobody could sign in).
  //   twoFactorCodeValidMinutes int   How long the code is valid (default 10).
  //   twoFactorMaxAttempts      int   Max. number of wrong codes before the user is locked (default 5).
  //   twoFactorResendCooldown   int   Min. seconds between two "resend code" requests (default 60).
  // ---------------------------------------------------------------------------------------------

  /**
   * Returns true if the second authentication step (code from email) is required for signing in.
   *
   * @return bool
   *
   */
  public function isTwoFactorEnabled(): bool
  {
    return $this->config()->getAsBool('twoFactorEnabled', false);
  }

  public function getTwoFactorCodeValidMinutes(): int
  {
    return max(1, $this->config()->getAsInteger('twoFactorCodeValidMinutes', 10));
  }

  public function getTwoFactorMaxAttempts(): int
  {
    return max(1, $this->config()->getAsInteger('twoFactorMaxAttempts', 5));
  }

  public function getTwoFactorResendCooldown(): int
  {
    return max(0, $this->config()->getAsInteger('twoFactorResendCooldown', 60));
  }

  /**
   * Returns the state of the unfinished sign-in (password verified, code not yet verified) or null.
   *
   * @return array{id_user: int, email: string, remember: bool, started_at: int, last_sent_at: int, valid_until: int}|null
   *
   */
  public function getPendingTwoFactor(): ?array
  {
    $pending = $this->sessionManager()->get(self::SESSION_KEY_PENDING_2FA);

    if (!is_array($pending) || (int) ($pending['id_user'] ?? 0) <= 0) {
      return null;
    }

    if ((int) ($pending['valid_until'] ?? 0) < time()) {
      $this->clearPendingTwoFactor();
      return null;
    }

    return $pending;
  }

  public function hasPendingTwoFactor(): bool
  {
    return $this->getPendingTwoFactor() !== null;
  }

  /**
   * Email of the user with an unfinished sign-in, masked for displaying (j***@example.com).
   *
   * @return string
   *
   */
  public function getPendingTwoFactorMaskedEmail(): string
  {
    return TwoFactorHelper::maskEmail((string) ($this->getPendingTwoFactor()['email'] ?? ''));
  }

  /**
   * Forgets the unfinished sign-in. The issued code stays in the database until it expires,
   * so the limit of failed attempts cannot be reset by starting over.
   *
   * @return void
   *
   */
  public function clearPendingTwoFactor(): void
  {
    // Note: SessionManager::unset() does not persist the change, therefore null is stored instead.
    if ($this->sessionManager()->get(self::SESSION_KEY_PENDING_2FA) !== null) {
      $this->sessionManager()->set(self::SESSION_KEY_PENDING_2FA, null);
    }
  }

  /**
   * User gave up the second step and returns to the first one (email and password).
   *
   * @return void
   *
   */
  public function cancelTwoFactor(): void
  {
    $this->twoFactorStatus = '';
    $this->clearPendingTwoFactor();
  }

  /**
   * Starts the second step for a user whose password has been verified: generates the code,
   * stores it as a token, sends it by email and remembers the unfinished sign-in in the session.
   * The user is NOT signed in.
   *
   * @param array $user Record of the user (as returned by User::findUsersByLogin()).
   * @param bool $remember Whether to issue the 'remember me' cookie once the sign-in is completed.
   *
   * @return bool true if the code was sent. On failure see $twoFactorStatus.
   *
   */
  protected function startTwoFactor(array $user, bool $remember): bool
  {
    $this->twoFactorStatus = '';

    $validUntil = $this->issueAndSendTwoFactorCode($user);
    if ($validUntil === null) {
      return false;
    }

    $this->sessionManager()->set(self::SESSION_KEY_PENDING_2FA, [
      'id_user' => (int) $user['id'],
      'email' => (string) ($user['email'] ?? ''),
      'remember' => $remember,
      'started_at' => time(),
      'last_sent_at' => time(),
      'valid_until' => $validUntil,
    ]);

    $this->twoFactorStatus = 'codeSent';
    return true;
  }

  /**
   * Issues a new code for the user and sends it by email.
   *
   * @param array $user
   *
   * @return int|null Timestamp until the code is valid, or null on failure (see $twoFactorStatus).
   *
   */
  private function issueAndSendTwoFactorCode(array $user): ?int
  {
    $idUser = (int) ($user['id'] ?? 0);

    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);

    $issued = $mToken->issueSignInCode(
      $idUser,
      $this->getTwoFactorCodeValidMinutes(),
      $this->getTwoFactorMaxAttempts()
    );

    if ($issued === false) {
      $this->logger()->warning("Sign-in code not issued to user {$idUser}: too many failed attempts.");
      $this->twoFactorStatus = 'tooManyAttempts';
      return null;
    }

    try {
      $email = trim((string) ($user['email'] ?? ''));
      if ($email === '') {
        throw new \Exception('The user has no email address.');
      }

      $name = trim(((string) ($user['first_name'] ?? '')) . ' ' . ((string) ($user['last_name'] ?? '')));

      /** @var EmailProvider $emailProvider */
      $emailProvider = $this->getService(EmailProvider::class);
      $emailProvider->sendSignInCodeEmail(
        $email,
        $name,
        (string) ($user['language'] ?? 'en'),
        $issued['token'],
        $this->getTwoFactorCodeValidMinutes()
      );
    } catch (\Throwable $e) {
      $this->logger()->error("Cannot send sign-in code to user {$idUser}: " . $e->getMessage());
      $mToken->deleteTokensWithLinks([$issued['id']]);
      $this->twoFactorStatus = 'sendFailed';
      return null;
    }

    $this->logger()->info("Sign-in code sent to user {$idUser}.");

    return (int) strtotime($issued['valid_to']);
  }

  /**
   * Verifies the code typed by the user. If it is valid, the user is signed in (stored in session).
   *
   * @param string $code
   *
   * @return bool true if the user has been signed in. On failure see $twoFactorStatus.
   *
   */
  public function verifyTwoFactorCode(string $code): bool
  {
    $this->twoFactorStatus = '';

    $pending = $this->getPendingTwoFactor();
    if ($pending === null) {
      $this->twoFactorStatus = 'codeExpired';
      return false;
    }

    $code = TwoFactorHelper::normalizeCode($code);
    if ($code === '') {
      // malformed code (cannot be a correct guess, so it is not counted as an attempt)
      $this->twoFactorStatus = 'invalidCode';
      return false;
    }

    $idUser = (int) $pending['id_user'];

    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);

    $result = $mToken->verifySignInCode($idUser, $code, $this->getTwoFactorMaxAttempts());

    if ($result === Token::CODE_OK) {
      $authResult = $this->loadActiveUser($idUser);

      if (empty($authResult)) {
        // the user has been deactivated or removed in the meantime
        $this->clearPendingTwoFactor();
        $this->twoFactorStatus = 'codeExpired';
        return false;
      }

      $this->signIn($authResult); // also discards the pending state

      if (!empty($pending['remember'])) {
        $this->initiateRememberMe();
      }

      $this->logger()->info("User {$idUser} signed in using the code sent by email.");

      return true;
    }

    if ($result === Token::CODE_INVALID) {
      $this->logger()->warning("Invalid sign-in code entered for user {$idUser}.");
      $this->twoFactorStatus = 'invalidCode';
    } elseif ($result === Token::CODE_LOCKED) {
      $this->logger()->warning("Sign-in code locked for user {$idUser}: too many failed attempts.");
      $this->clearPendingTwoFactor();
      $this->twoFactorStatus = 'tooManyAttempts';
    } else {
      $this->clearPendingTwoFactor();
      $this->twoFactorStatus = 'codeExpired';
    }

    return false;
  }

  /**
   * Sends a new code to the user with an unfinished sign-in. The failed attempts counter is preserved.
   *
   * @return bool true if a new code was sent. On failure see $twoFactorStatus.
   *
   */
  public function resendTwoFactorCode(): bool
  {
    $this->twoFactorStatus = '';

    $pending = $this->getPendingTwoFactor();
    if ($pending === null) {
      $this->twoFactorStatus = 'codeExpired';
      return false;
    }

    if (time() - (int) ($pending['last_sent_at'] ?? 0) < $this->getTwoFactorResendCooldown()) {
      $this->twoFactorStatus = 'resendTooSoon';
      return false;
    }

    $user = $this->loadActiveUser((int) $pending['id_user']);
    if (empty($user)) {
      $this->clearPendingTwoFactor();
      $this->twoFactorStatus = 'codeExpired';
      return false;
    }

    $validUntil = $this->issueAndSendTwoFactorCode($user);
    if ($validUntil === null) {
      // no valid code exists any more (locked or not delivered), the user has to start over
      $this->clearPendingTwoFactor();
      return false;
    }

    $pending['last_sent_at'] = time();
    $pending['valid_until'] = $validUntil;
    $this->sessionManager()->set(self::SESSION_KEY_PENDING_2FA, $pending);

    $this->twoFactorStatus = 'codeResent';
    return true;
  }

  /**
   * Loads the user together with roles, teams, ... Returns empty array if the user is not active.
   *
   * @param int $idUser
   *
   * @return array
   *
   */
  private function loadActiveUser(int $idUser): array
  {
    /** @var Models\User $userModel */
    $userModel = $this->createUserModel();

    if ($userModel->record->where('id', $idUser)->where('is_active', '<>', 0)->count() == 0) {
      return [];
    }

    return $userModel->loadUser($idUser);
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
   * @return void
   * 
   */
  public function signOut()
  {
    if (isset($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe'])) {
      unset($_COOKIE[$this->config()->getAsString('accountUid') . '-rememberMe']);
      setcookie($this->config()->getAsString('accountUid') . '-rememberMe', '', time() - 3600, "/");
    }

    /** @var Token */
    $mToken = $this->getModel(Token::class);
    /** @var UserHasToken */
    $mUserHasToken = $this->getModel(UserHasToken::class);

    $tokenIds = $mUserHasToken->record->where('id_user', $this->getUserId())->get(['id', 'id_token']);

    foreach ($tokenIds as $entry) {
      if ($mToken->record->where('id', $entry['id_token'])->where('type', Token::TOKEN_TYPE_USER_REMEMBER_ME)->count() > 0) {
        $mUserHasToken->record->where('id', $entry['id'])->delete();
      }
      $mToken->record->where('id', $entry['id_token'])->where('type', Token::TOKEN_TYPE_USER_REMEMBER_ME)->delete();
    }

    parent::signOut();
  }

}
