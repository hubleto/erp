<?php

namespace HubletoMain\Core;

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

    $mToken = new Token($this->app);
    $mToken->eloquent->create([
      'login' => $login,
      'token' => bin2hex(random_bytes(16)),
    ]);
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
      $mUser->eloquent
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