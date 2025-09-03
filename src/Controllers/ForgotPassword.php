<?php declare(strict_types=1);

namespace Hubleto\Erp\Controllers;

class ForgotPassword extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'Hubleto\\Erp\\Loader::Controllers\\ForgotPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      $this->authProvider()->forgotPassword();
      $this->viewParams = ['status' => 1];
      $this->setView('@hubleto-main/ForgotPassword.twig');
    } else {
      $this->viewParams = ['status' => 0];
      $this->setView('@hubleto-main/ForgotPassword.twig');
    }
  }

}
