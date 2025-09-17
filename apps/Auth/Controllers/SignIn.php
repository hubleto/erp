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

    $incorrectLogin = $_COOKIE['incorrectLogin'] ?? '';
    if (isset($_COOKIE['incorrectLogin'])) {
      setcookie('incorrectLogin', '', time() - 3600);
    }

    $this->viewParams = [
      'status' => $incorrectLogin == "1",
      'login' => $this->router()->urlParamAsString('user'),
    ];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $authProvider->auth();
    }

    if ($authProvider->isUserInSession()) {
      $this->router()->redirectTo('');
      return;
    }

    $this->setView('@Hubleto:App:Community:Auth/SignIn.twig');
  }

}
