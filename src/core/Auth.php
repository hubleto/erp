<?php

namespace HubletoMain\Core;

class Auth extends \ADIOS\Auth\Providers\DefaultProvider {

  public \HubletoMain $main;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->main = $main;
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
        ->where('id', $this->user['id'] ?? 0)
        ->update(['language' => $setLanguage])
      ;
      $this->user['language'] = $setLanguage;
      $this->updateUserInSession($this->user);

      $this->main->router->redirectTo('');
    }

    if (strlen((string) ($this->user['language'] ?? '')) != 2) $this->user['language'] = 'en';
  }

}