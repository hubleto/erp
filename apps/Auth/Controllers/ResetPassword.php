<?php declare(strict_types=1);

namespace Hubleto\App\Community\Auth\Controllers;



use Hubleto\App\Community\Auth\Models\Token;
use Hubleto\App\Community\Auth\Models\User;
use Hubleto\App\Community\Auth\Models\UserHasToken;

class ResetPassword extends \Hubleto\Erp\Controller
{
  public bool $requiresAuthenticatedUser = false;
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Token $mToken */
    $mToken = $this->getModel(Token::class);
    $idToken = $mToken->validateToken($this->router()->urlParamAsString('token'), Token::TOKEN_TYPE_USER_FORGOT_PASSWORD);

    if ($this->router()->urlParamAsString('token') == '' || !$idToken) {
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
        $this->getService(\Hubleto\Framework\AuthProvider::class)->resetPassword();

        $this->router()->redirectTo('');
      }
    }

    /** @var UserHasToken $mJunctionTable */
    $mJunctionTable = $this->getModel(\Hubleto\App\Community\Auth\Models\UserHasToken::class);
    $idUser = $mJunctionTable->record->where('id_token', $idToken)->first()?->id_user;

    $mUser = $this->getModel(User::class);
    $passwordHash = $mUser->record->where('id', $idUser)->first()?->password;

    $this->viewParams = ['status' => false, 'welcome' => $passwordHash == ''];
    $this->setView('@Hubleto:App:Community:Auth/ResetPassword.twig');
  }

}
