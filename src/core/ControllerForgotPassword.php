<?php

namespace HubletoMain\Core;

class ControllerForgotPassword extends \ADIOS\Core\Controller {

  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\ForgotPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      $this->app->auth->forgotPassword();
      $this->app->router->redirectTo('');
    }

    $this->setView('@hubleto/ForgotPassword.twig');
  }

}