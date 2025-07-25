<?php

namespace HubletoMain\Controllers;

class ControllerSignIn extends \HubletoMain\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\SignIn';

  public function prepareView(): void
  {
    parent::prepareView();
    $incorrectLogin = $_COOKIE['incorrectLogin'] ?? '';
    if (isset($_COOKIE['incorrectLogin'])) {
      setcookie('incorrectLogin', '', time() - 3600);
    }

    $this->setView('@hubleto/SignIn.twig', [
      'status' => $incorrectLogin == "1",
      'login' => $this->main->urlParamAsString('user'),
    ]);
  }

}
