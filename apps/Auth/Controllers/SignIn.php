<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;

use Hubleto\App\Community\Auth\AuthProvider;
use Hubleto\App\Community\Auth\TwoFactorHelper;

/**
 * Sign-in screen. Consists of two steps:
 *
 *   1. email + password
 *   2. 6-digit code sent to the user's email (when the second step is enabled, see AuthProvider::isTwoFactorEnabled())
 *
 * Both steps are handled here (and not in separate routes), because the sign-in screen is shown
 * on whatever URL an unauthenticated user requests and the form posts back to that very URL.
 */
class SignIn extends \Hubleto\Framework\Controllers\SignIn
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var AuthProvider $authProvider */
    $authProvider = $this->getService(AuthProvider::class);

    // 'remember me' cookie signs the user in without the second step, by design
    $authProvider->authenticateRememberedUser();

    $passwordReset = ($_COOKIE['passwordReset'] ?? '') == "1";
    $incorrectLogin = ($_COOKIE['incorrectLogin'] ?? '') == "1";

    $status = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if ($this->router()->urlParamAsBool('twofa-step')) {
        // step 2: code, "resend code" or "back to sign in"
        $status = $this->processSecondStep($authProvider);
      } else {
        // step 1: email and password
        $authProvider->auth();

        if (!$authProvider->isUserInSession()) {
          if ($authProvider->hasPendingTwoFactor()) {
            $status = $authProvider->twoFactorStatus;
          } elseif (in_array($authProvider->twoFactorStatus, ['sendFailed', 'tooManyAttempts'], true)) {
            $status = $authProvider->twoFactorStatus;
          } else {
            $incorrectLogin = true;
            $status = "incorrectLogin";
          }
        }
      }
    }

    if ($authProvider->isUserInSession()) {
      $this->logger()->info("Redirecting to home page, user is in session.");
      $this->router()->redirectTo('', 302, true);
      return;
    }

    if ($incorrectLogin && $status === '') {
      $status = "incorrectLogin";
    }

    if ($incorrectLogin) {
      setcookie('incorrectLogin', '', time() - 1000, '/');
    }

    if ($passwordReset) {
      setcookie('passwordReset', '', time() - 1000, '/');
      if ($status === '') {
        $status = "passwordReset";
      }
    }

    $this->viewParams = [
      'status' => $status,
      'login' => $this->router()->urlParamAsString('user'),
    ];

    $this->viewParams['uiTheme'] = $this->config()->getAsString('uiTheme', 'default');

    if ($authProvider->hasPendingTwoFactor()) {
      $this->viewParams['maskedEmail'] = $authProvider->getPendingTwoFactorMaskedEmail();
      $this->viewParams['codeLength'] = TwoFactorHelper::CODE_LENGTH;
      $this->setView('@Hubleto:App:Community:Auth/SignInCode.twig');
    } else {
      $this->setView('@Hubleto:App:Community:Auth/SignIn.twig');
    }
  }

  /**
   * Processes the form of the second step.
   *
   * @param AuthProvider $authProvider
   *
   * @return string Status to be displayed.
   *
   */
  private function processSecondStep(AuthProvider $authProvider): string
  {
    if (!$authProvider->hasPendingTwoFactor()) {
      // the code has expired or the session was lost => start over
      return 'codeExpired';
    }

    $action = $this->router()->urlParamAsString('twofa-action');

    if ($action === 'cancel') {
      $authProvider->cancelTwoFactor();
      return '';
    }

    if ($action === 'resend') {
      $authProvider->resendTwoFactorCode();
      return $authProvider->twoFactorStatus;
    }

    $authProvider->verifyTwoFactorCode($this->router()->urlParamAsString('code'));
    return $authProvider->twoFactorStatus;
  }

}
