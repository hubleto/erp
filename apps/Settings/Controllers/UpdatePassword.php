<?php

namespace Hubleto\App\Community\Settings\Controllers;

use Hubleto\App\Community\Auth\Models\User;

class UpdatePassword extends \Hubleto\Erp\Controller
{

  public bool $permittedForAllUsers = true;

  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'update-password', 'content' => $this->translate('Update password') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    $submitted = $this->router()->urlParamAsBool('submitted', false);
    $password1 = $this->router()->urlParamAsString('password_1', '');
    $password2 = $this->router()->urlParamAsString('password_2', '');

    $errors = [];
    $status = false;

    if ($submitted) {
      if ($password1 !== $password2) {
        $errors[] = $this->translate('Passwords do not match.');
      } elseif (
        strlen($password1) < 8
        || !preg_match('~[A-Z]~', $password1)
        || !preg_match('~[a-z]~', $password1)
        || !preg_match('~[0-9]~', $password1)
        || !preg_match('~[\W_]~', $password1)
      ) {
        $errors[] = $this->translate('Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character.');
      }

      if (count($errors) == 0) {
        /** @var User */
        $mUser = $this->getModel(User::class);
        $mUser->updatePassword($this->authProvider()->getUserId(), $password1);
        $status = true;
      }
    }

    $this->viewParams['errors'] = $errors;
    $this->viewParams['submitted'] = $submitted;
    $this->viewParams['status'] = $status;

    $this->setView('@Hubleto:App:Community:Settings/UpdatePassword.twig');
  }

}
