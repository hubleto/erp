<?php

namespace HubletoMain\Core\Controllers;

use HubletoApp\Community\Settings\Models\User;
use HubletoMain\Core\Models\Token;

class ControllerResetPassword extends \ADIOS\Core\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'ADIOS\\Core\\Loader::Controllers\\ResetPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    $mToken = $this->app->di->create(Token::class);
    if ($this->app->urlParamAsString('token') == '' || $mToken->record
        ->where('token', $_GET['token'])
        ->where('valid_to', '>', date('Y-m-d H:i:s'))
        ->where('type', 'reset-password')
        ->count() <= 0) {
      $this->app->router->redirectTo('');
    }

    $password = $this->app->urlParamAsString('password');
    $passwordConfirm = $this->app->urlParamAsString('password_confirm');

    if (
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && (!empty($password)
      || !empty($passwordConfirm))
    ) {

      if ($password !== $passwordConfirm) {
        $this->setView('@hubleto/ResetPassword.twig', ['error' => 'Passwords do not match.']);
        return;
      } elseif (strlen($password) < 8 || !preg_match('~[0-9]+~', $password)) {
        $this->setView('@hubleto/ResetPassword.twig', ['error' => 'Password must be at least 8 characters long and must contain at least one numeric character.']);
        return;
      } else {
        $this->app->auth->resetPassword();

        $this->app->router->redirectTo('');
      }
    }

    $login = $mToken->record
      ->where('token', $_GET['token'])
      ->where('valid_to', '>', date('Y-m-d H:i:s'))
      ->where('type', 'reset-password')->first()->login;

    $mUser = $this->app->di->create(User::class);
    $passwordHash = $mUser->record->where('login', $login)->first()->password;

    $this->setView('@hubleto/ResetPassword.twig', ['status' => false, 'welcome' => $passwordHash == '']);
  }

}
