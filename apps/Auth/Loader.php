<?php

namespace Hubleto\App\Community\Auth;

use Hubleto\Framework\DependencyInjection;

class Loader extends \Hubleto\Framework\App
{

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->router()->get([
      '/^api\/?$/' => Controllers\Home::class,
      '/^reset-password$/' => Controllers\ResetPassword::class,
      '/^forgot-password$/' => Controllers\ForgotPassword::class,
    ]);

    DependencyInjection::setServiceProviders([
      \Hubleto\Framework\AuthProvider::class => AuthProvider::class,
      \Hubleto\Framework\Controllers\SignIn::class => Controllers\SignIn::class,
      \Hubleto\Framework\Models\User::class => \Hubleto\App\Community\Settings\Models\User::class,
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\User::class)->dropTableIfExists()->install();
      $this->getModel(Models\Token::class)->dropTableIfExists()->install();
      $this->getModel(\Hubleto\App\Community\Settings\Models\UserRole::class)->dropTableIfExists()->install();
    } else if ($round == 2) {
      $this->getModel(Models\UserHasToken::class)->dropTableIfExists()->install();
      $this->getModel(\Hubleto\App\Community\Settings\Models\UserHasRole::class)->dropTableIfExists()->install();
    }
  }

}
