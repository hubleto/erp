<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;



use Hubleto\App\Community\Auth\AuthProvider;

class SignIn extends \Hubleto\Framework\Controllers\SignIn
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /* @var AuthProvider $authProvider */
    $authProvider = $this->getService(AuthProvider::class);

    $authProvider->authenticateRememberedUser();

    $passwordReset = ($_COOKIE['passwordReset'] ?? '') == "1";
    $incorrectLogin = ($_COOKIE['incorrectLogin'] ?? '') == "1";

    $status = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $authProvider->auth();

      if (!$authProvider->isUserInSession()) {
        $incorrectLogin = true;
        $status = "incorrectLogin";
      }
    }

    if ($authProvider->isUserInSession()) {
      $this->router()->redirectTo('');
      return;
    }

    if ($incorrectLogin) {
      setcookie('incorrectLogin', '', time() - 1000, '/');
      $status = "incorrectLogin";
    }

    if ($passwordReset) {
      setcookie('passwordReset', '', time() - 1000, '/');
      $status = "passwordReset";
    }

    $this->viewParams = [
      'status' => $status,
      'login' => $this->router()->urlParamAsString('user'),
    ];

    $this->setView('@Hubleto:App:Community:Auth/SignIn.twig');
  }

}
