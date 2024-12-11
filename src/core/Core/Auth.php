<?php

namespace CeremonyCrmApp\Core;

class Auth extends \ADIOS\Auth\Providers\DefaultProvider {

  public function auth()
  {
    parent::auth();

    $setLanguage = $this->app->params['set-language'] ?? '';

    if (
      !empty($setLanguage)
      && !empty(\CeremonyCrmMod\Core\Settings\Models\User::ENUM_LANGUAGES[$setLanguage])
    ) {
      $mUser = new \CeremonyCrmMod\Core\Settings\Models\User($this->app);
      $mUser->eloquent
        ->where('id', $this->user['id'])
        ->update(['language' => $setLanguage])
      ;
      $this->user['language'] = $setLanguage;
      $this->updateUserInSession($this->user);

      $this->app->router->redirectTo('');
    }
  }

}