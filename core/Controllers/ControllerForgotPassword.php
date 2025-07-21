<?php

namespace HubletoMain\Core\Controllers;

class ControllerForgotPassword extends \ADIOS\Core\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\ForgotPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      $this->app->auth->forgotPassword();
      $this->setView('@hubleto/ForgotPassword.twig', ['status' => 1]);
    } else {
      $this->setView('@hubleto/ForgotPassword.twig', ['status' => 0]);
    }
  }

}
