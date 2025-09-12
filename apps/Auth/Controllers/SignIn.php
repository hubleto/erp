<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;

class SignIn extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();
    $incorrectLogin = $_COOKIE['incorrectLogin'] ?? '';
    if (isset($_COOKIE['incorrectLogin'])) {
      setcookie('incorrectLogin', '', time() - 3600);
    }

    $this->viewParams = [
      'status' => $incorrectLogin == "1",
      'login' => $this->router()->urlParamAsString('user'),
    ];

    $this->setView('@Hubleto:App:Community:Auth/SignIn.twig');
  }

}
