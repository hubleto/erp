<?php declare(strict_types=1);

namespace HubletoMain\Controllers;

use HubletoApp\Community\Settings\Models\User;
use Hubleto\Framework\Models\Token;

class ResetPassword extends \HubletoMain\Controller
{
  public bool $requiresUserAuthentication = false;
  public bool $hideDefaultDesktop = true;
  public string $translationContext = 'HubletoMain\\Loader::Controllers\\ResetPassword';

  public function prepareView(): void
  {
    parent::prepareView();

    $mToken = $this->getService(Token::class);
    if ($this->getRouter()->urlParamAsString('token') == '' || $mToken->record
        ->where('token', $_GET['token'])
        ->where('valid_to', '>', date('Y-m-d H:i:s'))
        ->where('type', 'reset-password')
        ->count() <= 0) {
      $this->getRouter()->redirectTo('');
    }

    $password = $this->getRouter()->urlParamAsString('password');
    $passwordConfirm = $this->getRouter()->urlParamAsString('password_confirm');

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
        $this->getAuth()->resetPassword();

        $this->getRouter()->redirectTo('');
      }
    }

    $login = $mToken->record
      ->where('token', $_GET['token'])
      ->where('valid_to', '>', date('Y-m-d H:i:s'))
      ->where('type', 'reset-password')->first()->login;

    $mUser = $this->getService(User::class);
    $passwordHash = $mUser->record->where('login', $login)->first()->password;

    $this->viewParams = ['status' => false, 'welcome' => $passwordHash == ''];
    $this->setView('@hubleto-main/ResetPassword.twig');
  }

}
