<?php

namespace HubletoMain\Core;

use HubletoApp\Community\Settings\Models\User;
use WaiBlue\src\core\Models\Token;

class AuthProvider extends \ADIOS\Auth\DefaultProvider {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->main = $main;

    $this->app->registerModel(\HubletoApp\Community\Settings\Models\User::class);
    $this->app->registerModel(\HubletoApp\Community\Settings\Models\UserRole::class);
    $this->app->registerModel(\HubletoApp\Community\Settings\Models\UserHasRole::class);
  }

  public function createUserModel(): \ADIOS\Core\Model
  {
    return new \HubletoApp\Community\Settings\Models\User($this->app);
  }

  public function forgotPassword(): void
  {
    $login = $this->app->urlParamAsString('login');

    $mUser = new User($this->app);
    if ($mUser->record->where('login', $login)->count() > 0) {
      $mToken = new Token($this->app); // todo: token creation should be done withing the token itself
      $mToken->record->create([
        'login' => $login,
        'token' => bin2hex(random_bytes(16)),
        'valid_to' => date('Y-m-d H:i:s', strtotime('+15 minutes')),
        'type' => 'reset-password'
      ]);
    }
  }

  public function resetPassword(): void {
    $mToken = new Token($this->app);
    $mUser = new User($this->app);

    $token = $mToken->record->where('token', $this->main->urlParamAsString('token'))->first();
    $user = $mUser->record->where('login', $token->login)->first();
    $oldPassword = $user->password;

    // this logic also does not belong here todo
    $user->update(['password' => password_hash($this->main->urlParamAsString('password'), PASSWORD_DEFAULT)]);

    if ($oldPassword == "") {
      $this->app->setUrlParam('login', $token->login);
      $token->delete();
      $this->app->setUrlParam('password', $this->main->urlParamAsString('password'));

      $this->app->auth->auth();
    } else {
      $token->delete();
    }
  }

  public function auth(): void
  {
    parent::auth();

    $setLanguage = $this->main->urlParamAsString('set-language');

    if (
      !empty($setLanguage)
      && !empty(\HubletoApp\Community\Settings\Models\User::ENUM_LANGUAGES[$setLanguage])
    ) {
      $mUser = new \HubletoApp\Community\Settings\Models\User($this->main);
      $mUser->record
        ->where('id', $this->getUserId())
        ->update(['language' => $setLanguage])
      ;
      $this->user['language'] = $setLanguage;

      if ($this->isUserInSession()) {
        $this->updateUserInSession($this->user);
      }

      $date = date("D, d M Y H:i:s", strtotime('+1 year')) . 'GMT';
      header("Set-Cookie: language={$setLanguage}; EXPIRES{$date};");

      $this->main->router->redirectTo($_SERVER['REQUEST_METHOD'] == 'POST' ? '?incorrectLogin=1' : '');
    }

    if (strlen((string) ($this->user['language'] ?? '')) != 2) $this->user['language'] = 'en';
  }

}