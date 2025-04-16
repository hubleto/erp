<?php

namespace HubletoMain\Core\Controllers;

use WaiBlue\src\core\Models\Token;

class ControllerResetPassword extends \ADIOS\Core\Controller {

  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\ResetPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    $mToken = new Token($this->app);
    if ($this->app->urlParamAsString('token') == '' || $mToken->record
        ->where('token', $_GET['token'])
        ->where('valid_to', '>', date('Y-m-d H:i:s'))
        ->count() <= 0)
      $this->app->router->redirectTo('');

    if ($_SERVER['REQUEST_METHOD'] === 'POST'
      && $this->app->urlParamAsString('password') != ''
      && $this->app->urlParamAsString('password_confirm') != '')
    {
      if ($this->app->urlParamAsString('password') !== $this->app->urlParamAsString('password_confirm')) {
        $this->setView('@hubleto/ResetPassword.twig', ['status' => true]);
      } else {
        $this->app->auth->resetPassword();

        $this->app->router->redirectTo('');
      }
    }

    $this->setView('@hubleto/ResetPassword.twig', ['status' => false]);
  }

}