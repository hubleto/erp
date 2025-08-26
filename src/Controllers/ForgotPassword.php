<?php declare(strict_types=1);

namespace HubletoMain\Controllers;

class ForgotPassword extends \HubletoMain\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'HubletoMain\\Loader::Controllers\\ForgotPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      $this->getAuthProvider()->forgotPassword();
      $this->viewParams = ['status' => 1];
      $this->setView('@hubleto-main/ForgotPassword.twig');
    } else {
      $this->viewParams = ['status' => 0];
      $this->setView('@hubleto-main/ForgotPassword.twig');
    }
  }

}
