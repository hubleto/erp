<?php

namespace Hubleto\App\Community\Auth;

use Hubleto\Framework\DependencyInjection;

class Loader extends \Hubleto\Erp\App
{

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->router()->get([
//      '/^api\/?$/' => Controllers\Home::class,
      '/^sign-in$/' => Controllers\SignIn::class,
      '/^reset-password$/' => Controllers\ResetPassword::class,
      '/^forgot-password$/' => Controllers\ForgotPassword::class,
    ]);

    DependencyInjection::setServiceProviders([
      \Hubleto\Framework\AuthProvider::class => AuthProvider::class,
      \Hubleto\Framework\Controllers\SignIn::class => Controllers\SignIn::class,
      \Hubleto\Framework\Models\User::class => \Hubleto\App\Community\Auth\Models\User::class,
    ]);

    $this->getService(\Hubleto\Framework\AuthProvider::class)->init();
  }

  // upgradeSchema
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\User::class)->upgradeSchema();
      $this->getModel(Models\Token::class)->upgradeSchema();
      $this->getModel(Models\UserHasToken::class)->upgradeSchema();
      $this->getModel(Models\UserRole::class)->upgradeSchema();
      $this->getModel(Models\UserHasRole::class)->upgradeSchema();

      $mUserRole = $this->getModel(Models\UserRole::class);
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_ADMINISTRATOR,
        'role' => $this->translate('Administrator'),
        'description' => $this->translate('Can do anything.'),
        'grant_all' => true,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_CHIEF_OFFICER,
        'role' => $this->translate('Chief Officer (default permissions)'),
        'description' => $this->translate('Can read all data and can modify most of the data. Does not have access to settings.'),
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_MANAGER,
        'role' => $this->translate('Manager (default permissions)'),
        'description' => $this->translate('Can read and modify all data that he/she owns or is manager.'),
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_EMPLOYEE,
        'role' => $this->translate('Employee (default permissions)'),
        'description' => $this->translate('In general, can read or modify only data that he/she owns.'),
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_ASSISTANT,
        'role' => $this->translate('Assistant (default permissions)'),
        'description' => $this->translate('Very similar to employee, but may be more limited in some certain situations.'),
        'grant_all' => false,
        'is_default' => true,
      ])['id'];
      $mUserRole->record->recordCreate([
        'id' => Models\UserRole::ROLE_EXTERNAL,
        'role' => $this->translate('External (default permissions)'),
        'description' => $this->translate('By default should not have access to anything. Access permissions must be enabled in settings by administrator.'),
        'grant_all' => false,
        'is_default' => true,
      ])['id'];

    }
  }

}
