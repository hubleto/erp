<?php

namespace HubletoMain\Core;

use HubletoApp\Community\Settings\Models\User;
use HubletoMain\Core\Token;

class ControllerResetPassword extends \ADIOS\Core\Controller {

  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\ResetPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    $mToken = new Token($this->app);
    $mUser = new User($this->app);
    if (!isset($_GET['token']) || $mToken->eloquent->where('token', $_GET['token'])->count() <= 0) $this->app->router->redirectTo('');

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password']) && isset($_POST['password_confirm'])) {
      if ($_POST['password'] !== $_POST['password_confirm']) {
        $this->setView('@hubleto/ResetPassword.twig', ['status' => true]);
      } else {
        $token = $mToken->eloquent->where('token', $_GET['token'])->first();
        $user = $mUser->eloquent->where('login', $token->login)->first();
        $oldPassword = $user->password;

        // this logic also does not belong here todo
        $user->update(['password' => password_hash($_POST['password'], PASSWORD_DEFAULT)]);

        if ($oldPassword == "") {
          $this->app->setUrlParam('login', $token->login);
          $this->app->setUrlParam('password', $_POST['password']);
          $token->delete();

          $this->app->auth->auth();
        }

        $this->app->router->redirectTo('');
      }
    }

    $this->setView('@hubleto/ResetPassword.twig', ['status' => false]);
  }

}