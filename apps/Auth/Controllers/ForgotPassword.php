<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;



use Hubleto\App\Community\Auth\AuthProvider;

class ForgotPassword extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
      $this->getService(AuthProvider::class)->forgotPassword();
      $this->viewParams = ['status' => 1];
      $this->setView('@Hubleto:App:Community:Auth/ForgotPassword.twig');
    } else {
      $this->viewParams = ['status' => 0];
      $this->setView('@Hubleto:App:Community:Auth/ForgotPassword.twig');
    }
  }

}
