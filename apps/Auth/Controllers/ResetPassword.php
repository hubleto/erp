<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;

use Hubleto\App\Community\Auth\Models\User;
use Hubleto\Framework\Models\Token;

class ResetPassword extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Token $mToken */
    $mToken = $this->getService(Token::class);
    if ($this->router()->urlParamAsString('token') == '' || !$mToken->validateToken($this->router()->urlParamAsString('token'))) {
      $this->router()->redirectTo('');
    }

    $password = $this->router()->urlParamAsString('password');
    $passwordConfirm = $this->router()->urlParamAsString('password_confirm');

    if (
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && (!empty($password)
      || !empty($passwordConfirm))
    ) {

      if ($password !== $passwordConfirm) {
        $this->viewParams = ['error' => 'Passwords do not match.'];
        $this->setView('@Hubleto:App:Community:Auth/ResetPassword.twig');
        return;
      } elseif (strlen($password) < 8 || !preg_match('~[0-9]+~', $password)) {
        $this->viewParams = ['error' => 'Password must be at least 8 characters long and must contain at least one numeric character.'];
        $this->setView('@Hubleto:App:Community:Auth/ResetPassword.twig');
        return;
      } else {
        $this->getService(AuthProvider::class)->resetPassword();

        $this->router()->redirectTo('');
      }
    }

    $login = $mToken->record
      ->where('token', $_GET['token'])
      ->where('valid_to', '>', date('Y-m-d H:i:s'))
      ->where('type', 'reset-password')->first()?->login;

    $mUser = $this->getService(User::class);
    $passwordHash = $mUser->record->where('login', $login)->first()?->password;

    $this->viewParams = ['status' => false, 'welcome' => $passwordHash == ''];
    $this->setView('@Hubleto:App:Community:Auth/ResetPassword.twig');
  }

}
