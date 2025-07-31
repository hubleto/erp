<?php declare(strict_types=1);

namespace HubletoMain\Controllers;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Models\Token;

class ControllerResetPassword extends \HubletoMain\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'Hubleto\\Core\\Loader::Controllers\\ResetPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    $mToken = $this->main->di->create(Token::class);
    if ($this->main->urlParamAsString('token') == '' || $mToken->record
        ->where('token', $_GET['token'])
        ->where('valid_to', '>', date('Y-m-d H:i:s'))
        ->where('type', 'reset-password')
        ->count() <= 0) {
      $this->main->router->redirectTo('');
    }

    $password = $this->main->urlParamAsString('password');
    $passwordConfirm = $this->main->urlParamAsString('password_confirm');

    if (
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && (!empty($password)
      || !empty($passwordConfirm))
    ) {

      if ($password !== $passwordConfirm) {
        $this->viewParams = ['error' => 'Passwords do not match.'];
        $this->setView('@hubleto-main/ResetPassword.twig');
        return;
      } elseif (strlen($password) < 8 || !preg_match('~[0-9]+~', $password)) {
        $this->viewParams = ['error' => 'Password must be at least 8 characters long and must contain at least one numeric character.'];
        $this->setView('@hubleto-main/ResetPassword.twig');
        return;
      } else {
        $this->main->auth->resetPassword();

        $this->main->router->redirectTo('');
      }
    }

    $login = $mToken->record
      ->where('token', $_GET['token'])
      ->where('valid_to', '>', date('Y-m-d H:i:s'))
      ->where('type', 'reset-password')->first()->login;

    $mUser = $this->main->di->create(User::class);
    $passwordHash = $mUser->record->where('login', $login)->first()->password;

    $this->viewParams = ['status' => false, 'welcome' => $passwordHash == ''];
    $this->setView('@hubleto-main/ResetPassword.twig');
  }

}
