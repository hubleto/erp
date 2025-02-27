<?php

namespace HubletoMain\Core;

class AuthProvider extends \ADIOS\Auth\Providers\DefaultProvider {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->main = $main;

    $this->app->registerModel(\HubletoApp\Community\Settings\Models\User::class);
    $this->app->registerModel(\HubletoApp\Community\Settings\Models\UserRole::class);
    $this->app->registerModel(\HubletoApp\Community\Settings\Models\UserHasRole::class);
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
      $this->updateUserInSession($this->user);

      $date = date("D, d M Y H:i:s", strtotime('+1 year')) . 'GMT';
      header("Set-Cookie: language={$setLanguage}; EXPIRES{$date};");

      $this->main->router->redirectTo('');
    }

    if (strlen((string) ($this->user['language'] ?? '')) != 2) $this->user['language'] = 'en';
  }

}