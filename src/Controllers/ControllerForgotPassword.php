<?php

namespace HubletoMain\Controllers;

class ControllerForgotPassword extends \HubletoMain\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'Hubleto\\Core\\Loader::Controllers\\ForgotPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      $this->main->auth->forgotPassword();
      $this->setView('@hubleto-main/ForgotPassword.twig', ['status' => 1]);
    } else {
      $this->setView('@hubleto-main/ForgotPassword.twig', ['status' => 0]);
    }
  }

}
